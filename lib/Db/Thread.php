<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity,
    \OCP\DB\Types as Types;

class Thread extends Entity implements JsonSerializable {
    protected $user_id;
    protected $name;

    public function __construct() {
        $this->addType('user_id',Types::STRING);
        $this->addType('name',Types::STRING);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
        ];
    }
}
