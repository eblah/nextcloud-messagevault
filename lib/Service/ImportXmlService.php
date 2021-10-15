<?php

namespace OCA\MessageVault\Service;

use Exception;
use OC\Files\Node\File;
use OC\User\User;
use OCA\MessageVault\Db\Address;
use OCA\MessageVault\Db\Attachment;
use OCA\MessageVault\Db\AttachmentMapper;
use OCA\MessageVault\Db\Message;
use OCA\MessageVault\Db\MessageMapper;
use OCA\MessageVault\Db\Thread;
use OCA\MessageVault\Db\ThreadAddress;
use OCA\MessageVault\Db\ThreadAddressMapper;
use OCA\MessageVault\Db\ThreadMapper;
use OCA\MessageVault\Storage\AttachmentStorage;
use OCP\Files\AlreadyExistsException;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IConfig;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;
use OCA\MessageVault\Error\ImportException;
use XMLReader;
use OCA\MessageVault\Db\AddressMapper;

class ImportXmlService {
	private $app_name;
	private $cache_address = [];
	private $cache_thread = [];
	private $exclude_numbers = [];

	private $address_mapper;
	private $attachment_mapper;
	private $message_mapper;
	private $thread_mapper;
	private $thread_address_mapper;
	private $attachment_storage;
	private $config;
	private $storage;
	/** @var LoggerInterface */
	private $logger;

	/** @var User */
	private $user;

	public function __construct(AddressMapper $address_mapper, MessageMapper $message_mapper,
								ThreadMapper $thread_mapper, ThreadAddressMapper $thread_address_mapper,
								AttachmentMapper $attachment_mapper, AttachmentStorage $attachment_storage,
								IConfig $config, IRootFolder $storage, LoggerInterface $logger,
								$appName) {
		$this->app_name = $appName;
		$this->address_mapper = $address_mapper;
		$this->message_mapper = $message_mapper;
		$this->thread_mapper = $thread_mapper;
		$this->thread_address_mapper = $thread_address_mapper;
		$this->attachment_storage = $attachment_storage;
		$this->attachment_mapper = $attachment_mapper;
		$this->storage = $storage;
		$this->config = $config;
		$this->logger = $logger;
	}

	private function cacheAddresses() {
		foreach($this->address_mapper->findAll($this->user) as $address) {
			$this->cache_address[$address->getAddress()] = $address->getId();
		}
	}

	private function cacheThread() {
		foreach($this->thread_mapper->findAllHashes($this->user) as $thread) {
			$this->cache_thread[$thread->getUniqueHash()] = $thread->getId();
		}
	}

	public function getNodeFromFilename(User $user, string $file): ?File {
		$user_folder = $this->storage->getUserFolder($user->getUID());

		try {
			return $user_folder->get($file);
		} catch(NotFoundException $e) {
			return null;
		}
	}

	public function getFilePath(User $user, int $file_id): ?string {
		$user_folder = $this->storage->getUserFolder($user->getUID());

		try {
			return $user_folder->getStorage()->getLocalFile(
				$user_folder->getById($file_id)[0]
					->getInternalPath()
			);
		} catch(NotFoundException $e) {
			return false;
		}
	}

	public function runImport(User $user, int $file_id) {
		$this->user = $user;

		$this->exclude_numbers = explode(',',
			$this->config->getUserValue($this->user->getUID(), $this->app_name, 'myAddress')
		);
		$this->cacheAddresses();
		$this->cacheThread();

		foreach($this->exclude_numbers as $exclude_number) {
			$this->findOrCreateAddress($exclude_number, $this->user->getDisplayName());
		}

		if(!($full_path = $this->getFilePath($user, $file_id))) {
			throw new ImportException('Could not find the file to import.');
		}

		$this->logger->info('Starting an import of ' . $full_path);
		$reader = new XMLReader();
		$reader->open($full_path, null, LIBXML_PARSEHUGE | LIBXML_HTML_NOIMPLIED | LIBXML_BIGLINES);

		while ($reader->read()) {
			if(!in_array($reader->name, ['mms', 'sms']) || $reader->nodeType !== XMLReader::ELEMENT) continue;

			try {
				$message_data = new SimpleXMLElement($reader->readOuterXml(), LIBXML_PARSEHUGE | LIBXML_HTML_NOIMPLIED | LIBXML_BIGLINES);
			} catch(Exception $e) {
				$this->logger->error('Could not parse xml: ' . $e->getMessage());
				continue;
			}

			if($reader->name == 'sms') $this->parseSms($message_data);
			if($reader->name == 'mms') $this->parseMms($message_data);
		}
		$this->logger->info('Completed import.');
	}

	private function findOrCreateAddress(string $address, string $name = null): int {
		if(!array_key_exists($address, $this->cache_address)) {
			$id = $this->address_mapper->insert((new Address())->fromParams([
				'address' => $address,
				'userId' => $this->user->getUID(),
				'name' => $name
			]))->getId();

			$this->cache_address[$address] = $id;
		}

		return $this->cache_address[$address];
	}

	private function findOrCreateNewThread(string $thread_name, array $address_ids): int {
		$hash = Thread::buildHash($address_ids, $this->user);

		if(!array_key_exists($hash, $this->cache_thread)) {
			$id = $this->thread_mapper->insert((new Thread())->fromParams([
				'userId' => $this->user->getUID(),
				'name' => $thread_name,
				'uniqueHash' => $hash
			]))->getId();

			$this->cache_thread[$hash] = $id;

			foreach($address_ids as $a_id) {
				// These are mainly only saved for documentation purposes to see who was on the thread
				$this->thread_address_mapper->insert((new ThreadAddress())->fromParams([
					'threadId' => $id,
					'addressId' => $a_id
				]));
			}
		}

		return $this->cache_thread[$hash];
	}

	private function getAddressId(string $address) {
		return $this->cache_address[$this->parseNumber($address)];
	}

	private function getThread(array $addresses, string $thread_name = null): int {
		$address_ids = [];
		if($thread_name === '(Unknown)') $thread_name = null;

		$parsed_addresses = [];
		foreach($addresses as $address) {
			$parsed_number = $this->parseNumber($address);

			if(in_array($parsed_number, $this->exclude_numbers)) continue;
			$parsed_addresses[] = $parsed_number;
		}

		foreach($parsed_addresses as $address) {
			$id = $this->findOrCreateAddress($address, count($parsed_addresses) === 1 ? $thread_name : null);

			$address_ids[$address] = $id;
		}

		if(count($address_ids) === 0) {
			throw new Exception('No numbers to add.');
		}

		// If the thread name was null for some reason, let's add the users phone number as the thread name
		if(empty($thread_name)) {
			$thread_name = implode(', ', array_keys($address_ids));
		}

		return $this->findOrCreateNewThread($thread_name, $address_ids);
	}

	private function createMessage(int $thread_id, int $address_id, int $date, int $rec, string $body = null): int {
		$hash = Message::buildHash($address_id, $date, $body); // Attachments could be attached to multi-messages

		$m_id = $this->message_mapper->doesHashExist($hash);
		if($m_id === null) {
			$entity = $this->message_mapper->insert((new Message())->fromParams([
				'threadId' => $thread_id,
				'addressId' => $address_id,
				'timestamp' => $date,
				'received' => $rec,
				'body' => $body,
				'uniqueHash' => $hash
			]));
			$m_id = $entity->getId();
		}

		return (int)$m_id;
	}

	private function parseSms($message_data): void {
		try {
			$thread_id = $this->getThread(
				[(string)$message_data['address']],
				(string)$message_data['contact_name']
			);

			$subject = (string)$message_data['subject'];
			$body = (string)$message_data['body'];

			if($body === 'null') $body = '';
			if($subject !== 'null') $body = $subject . "\n" . $body;

			if($body === '') return; // Blank message

			$this->createMessage(
				$thread_id,
				$this->getAddressId($message_data['address']),
				$message_data['date'] / 1000,
				(int)$message_data['type'] === 1 ? 1 : 0,
				$body
			);
		} catch(Exception $e) {
			$this->logger->error('Malformed SMS message: ' . $e->getMessage());
			return; // Bad message
		}
	}

	private function parseMms($message_data) {
		try {
			$addresses = [];

			foreach($message_data->addrs[0] as $address) {
				$addresses[] = (string)$address['address'];
			}
			if(!count($addresses)) {
				var_dump($message_data['readable_date']);
				die;
			}
			$thread_id = $this->getThread(
				$addresses,
				(string)$message_data['contact_name']
			);

			if(!empty($message_data->parts)) {
				foreach($message_data->parts[0] as $part) {
					if($part['ct'] == 'application/smil') continue;

					$body = $part['text'] == 'null' ? null : $part['text'];

					// We don't want a blank message; however, we DO want to add attachments to (potentially)
					// blank messages, so they can be anchored to the thread
					if($body === null && $part['ct'] == 'text/plain') continue;

					$message_id = $this->createMessage(
						$thread_id,
						$this->getAddressId($addresses[0]),
						$message_data['date'] / 1000,
						(int)$message_data['m_type'] === 132 ? 1 : 0,
						$body
					);

					if($part['ct'] != 'text/plain') {
						try {
							$this->createAttachment($message_id, $thread_id, $part['ct'],
								$part['data'], $part['cl'] === 'null' ? null : $part['cl']);
						} catch (Exception $e) {
							echo $e->getMessage();
						}
					}
				}
			}
		} catch(Exception $e) {
			$this->logger->error('Malformed MMS message: ' . $e->getMessage());
			return; // Bad message
		}
	}

	private function createAttachment(int $message_id, int $thread_id, string $type, string $base64_data, string $name = null) {
		$hash = Attachment::buildHash($message_id, $name, $this->user);

		$attachment_id = $this->attachment_mapper->doesHashExist($hash);

		if($attachment_id === null) {
			$data = base64_decode($base64_data, true);
			if($data === false) throw new Exception('Invalid attachment.');

			$dims = $this->attachment_storage->getDimensions('data://text/plain;base64,' . $base64_data, $type);

			$entity = $this->attachment_mapper->insert((new Attachment())->fromParams([
				'messageId' => $message_id,
				'name' => $name === 'null' ? null : $name,
				'filetype' => $type,
				'width' => $dims[0] ?? null,
				'height' => $dims[1] ?? null,
				'uniqueHash' => $hash
			]));
			$attachment_id = $entity->getId();

			/** @todo This needs to be moved to the attachment service */
			try {
				$this->attachment_storage->writeFile($this->user, $thread_id, $attachment_id, $data);
			} catch(AlreadyExistsException $e) {
				// Should be ok since only our app should be writing to files here
			}
		}
	}

	// @todo this really doesn't support anything outside of US numbers
	public function parseNumber($number) {
		// https://stackoverflow.com/a/10741461
		$test = str_replace(['(', ')', '+', ' ', '-', '.'], '', $number);
		if(is_numeric($test) && ($test = preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '$1$2$3', $test)) !== false) {
			return $test;
		} else {
			return $number;
		}
	}
}
