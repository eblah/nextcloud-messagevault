<?php
namespace OCA\MessageVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;
use \OCP\DB\Types as Types;

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
			'address' => $this->address,
			'name' => $this->name,
		];
	}
}
