<?php

namespace OCA\SmsBackupVault\Service;

use Exception;

use OCA\SmsBackupVault\Db\AddressMapper;
use OCA\SmsBackupVault\Db\AttachmentMapper;
use OCA\SmsBackupVault\Db\Message;
use OCA\SmsBackupVault\Db\MessageMapper;
use OCA\SmsBackupVault\Db\ThreadAddressMapper;
use OCA\SmsBackupVault\Db\ThreadMapper;
use OCA\SmsBackupVault\Storage\AttachmentStorage;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class ThreadService {
    private $thread_mapper;
    private $thread_address_mapper;
    private $message_mapper;

    public function __construct(ThreadMapper $thread_mapper, ThreadAddressMapper $thread_address_mapper,
                                MessageMapper $message_mapper,
                                $UserId) {
        $this->thread_address_mapper = $thread_address_mapper;
        $this->thread_mapper = $thread_mapper;
        $this->message_mapper = $message_mapper;
    }

    public function findAll(string $user_id): array {
        return $this->thread_mapper->findAll($user_id);
    }

    public function loadThread(string $user_id, int $id, int $page = 0, int $limit = 100) {
        $info = $this->thread_mapper->find($id, $user_id);
        if(empty($info)) return false;

        $messages = $this->message_mapper->findAll($id, $page, $limit);

        return [
            'details' => $info[0],
            'messages' => $messages
        ];
    }
}
