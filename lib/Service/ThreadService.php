<?php

namespace OCA\MessageVault\Service;

use OCA\MessageVault\Db\MessageMapper;
use OCA\MessageVault\Db\ThreadAddressMapper;
use OCA\MessageVault\Db\ThreadMapper;
use OCA\MessageVault\Storage\AttachmentStorage;
use OCP\IUserSession;

class ThreadService {
	private $thread_mapper;
	private $thread_address_mapper;
	private $message_mapper;

	public function __construct(ThreadMapper $thread_mapper, ThreadAddressMapper $thread_address_mapper,
								MessageMapper $message_mapper) {
		$this->thread_address_mapper = $thread_address_mapper;
		$this->thread_mapper = $thread_mapper;
		$this->message_mapper = $message_mapper;
	}

	public function findAll(IUserSession $user): array {
		return $this->thread_mapper->findAll($user->getUser());
	}

	/**
	 * Verifies the user has permission to read this thread
	 * @param string $user_id
	 * @return bool
	 */
	public function hasPermission(IUserSession $user, int $id): bool {
		$info = $this->thread_mapper->find($id, $user->getUser());
		return !empty($info);
	}

	public function getThreadDetails(IUserSession $user, int $id): array {
		$details = $this->thread_mapper->find($id, $user->getUser());
		if(empty($details)) return [];

		return [
			'id' => $details[0]->getId(),
			'name' => $details[0]->getName(),
			'total' => $this->message_mapper->getMessageCount($id)
		];
	}
}
