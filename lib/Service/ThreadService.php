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
use OCP\IUser;
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

    public function getMessageCount($thead_id): int {
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
