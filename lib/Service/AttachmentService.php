<?php

namespace OCA\SmsBackupVault\Service;

use Exception;

use OCA\SmsBackupVault\Db\AttachmentMapper;
use OCA\SmsBackupVault\Storage\AttachmentStorage;
use OCP\Files\NotFoundException;

class AttachmentService {
	private $mapper;
	private $storage;

	public function __construct(AttachmentMapper $mapper, AttachmentStorage $storage) {
		$this->mapper = $mapper;
		$this->storage = $storage;
	}

	public function getAttachments(int $thread_id, array $message_ids = []): array {
		$attachments = $this->mapper->findAllByMessageId($message_ids);

		foreach($attachments as $file) {
			try {
				$file->setUrl($this->storage->getFileUrl($thread_id, $file->getId()));
			} catch(NotFoundException $e) {
				continue;
			}
		}

		return $attachments;
	}
}
