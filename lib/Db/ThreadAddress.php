<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity,
    \OCP\DB\Types as Types;

class ThreadAddress extends Entity implements JsonSerializable {
    protected $thread_id;
    protected $address_id;

    public function __construct() {
        $this->addType('thread_id',Types::INTEGER);
        $this->addType('address_id',Types::INTEGER);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->thread_id,
            'user_id' => $this->address_id,
        ];
    }
}
