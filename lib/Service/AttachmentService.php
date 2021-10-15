<?php

namespace OCA\MessageVault\Service;

use OCA\MessageVault\Db\AttachmentMapper;
use OCA\MessageVault\Storage\AttachmentStorage;
use OCP\Files\NotFoundException;
use OCP\IUserSession;

class AttachmentService {
	private $mapper;
	private $storage;

	public function __construct(AttachmentMapper $mapper, AttachmentStorage $storage) {
		$this->mapper = $mapper;
		$this->storage = $storage;
	}

	public function getAttachments(IUserSession $user_session, int $thread_id, array $message_ids = []): array {
		$attachments = $this->mapper->findAllByMessageId($message_ids);

		foreach($attachments as $file) {
			try {
				$file->setUrl($this->storage->getFileUrl($user_session->getUser(), $thread_id, $file->getId()));
			} catch(NotFoundException $e) {
				continue;
			}
		}

		return $attachments;
	}
}
