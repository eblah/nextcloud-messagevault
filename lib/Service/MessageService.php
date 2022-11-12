<?php

namespace OCA\MessageVault\Service;

use OCA\MessageVault\Db\MessageMapper;

class MessageService {
	private $message_mapper;

	public function __construct(MessageMapper $message_mapper) {
		$this->message_mapper = $message_mapper;
	}

	public function findAll(int $thread_id, int $page = 0, int $limit = 100, string $search = null) {
		return $this->message_mapper->findAll($thread_id, $page, $limit, $search);
	}

	public function findAllMessageIds(int $thread_id): array {
		return $this->message_mapper->findAllMessageIds($thread_id);
	}

	public function deleteThreadMessages(int $thread_id): void {
		$this->message_mapper->deleteThreadMessages($thread_id);
	}
}
