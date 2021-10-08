<?php

namespace OCA\SmsBackupVault\Service;

use Exception;

use OCA\SmsBackupVault\Db\AddressMapper;
use OCA\SmsBackupVault\Db\MessageMapper;
use OCA\SmsBackupVault\Db\ThreadAddressMapper;
use OCA\SmsBackupVault\Db\ThreadMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\NotesTutorial\Db\Note;
use OCA\NotesTutorial\Db\NoteMapper;
use OCP\IUserSession;


class AddressService {
    private $address_mapper;

    public function __construct(AddressMapper $address_mapper) {
        $this->address_mapper = $address_mapper;
    }

    public function findAll(IUserSession $user) {
        return $this->address_mapper->findAll($user->getUser(), ['id', 'address', 'name']);
    }
}
