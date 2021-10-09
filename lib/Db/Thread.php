<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OC\User\User;
use OCP\AppFramework\Db\Entity,
	\OCP\DB\Types as Types;

class Thread extends Entity implements JsonSerializable {
	protected $userId;
	protected $name;
	protected $uniqueHash;

	public function __construct() {
		$this->addType('userId',Types::STRING);
		$this->addType('name',Types::STRING);
		$this->addType('uniqueHash',Types::STRING);
	}

	public static function buildHash(array $address_ids, User $user): string {
		sort($address_ids);
		return md5(implode(',', $address_ids) . '|' . $user->getUID());
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}
}
