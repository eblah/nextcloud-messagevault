<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity,
    \OCP\DB\Types as Types;

class Address extends Entity implements JsonSerializable {
    protected $address;
    protected $userId;
    protected $name;

    public function __construct() {
        $this->addType('userId',Types::STRING);
        $this->addType('address',Types::STRING);
        $this->addType('name',Types::STRING);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'address' => $this->address,
            'name' => $this->name,
        ];
    }
}
