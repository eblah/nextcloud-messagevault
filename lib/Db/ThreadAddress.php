<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity,
	\OCP\DB\Types as Types;

class ThreadAddress extends Entity implements JsonSerializable {
	protected $threadId;
	protected $addressId;

	public function __construct() {
		$this->addType('threadId',Types::INTEGER);
		$this->addType('addressId',Types::INTEGER);
	}

	public function jsonSerialize() {
		return [
			'threadId' => $this->threadId,
			'addressId' => $this->addressId,
		];
	}
}
