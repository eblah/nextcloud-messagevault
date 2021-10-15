<?php

namespace OCA\MessageVault\Service;

use OCA\MessageVault\Db\MessageMapper;
use OCA\MessageVault\Db\ThreadAddressMapper;
use OCA\MessageVault\Db\ThreadMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\NotesTutorial\Db\Note;
use OCA\NotesTutorial\Db\NoteMapper;
use OCP\IUserSession;

class MessageService {
	private $message_mapper;

	public function __construct(MessageMapper $message_mapper) {
		$this->message_mapper = $message_mapper;
	}

	public function findAll(int $thread_id, int $page = 0, int $limit = 100) {
		$messages = $this->message_mapper->findAll($thread_id, $page, $limit);

		return $messages;
	}
}
