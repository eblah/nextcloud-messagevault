<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OC\User\User;
use OCP\AppFramework\Db\Entity,
	\OCP\DB\Types as Types;

class Attachment extends Entity implements JsonSerializable {
	protected $messageId;
	protected $name;
	protected $filetype;
	protected $uniqueHash;
	protected $width;
	protected $height;

	protected $url;

	public function __construct() {
		$this->addType('messageId',Types::INTEGER);
		$this->addType('name',Types::STRING);
		$this->addType('filetype',Types::STRING);
		$this->addType('width',Types::INTEGER);
		$this->addType('height',Types::INTEGER);
		$this->addType('uniqueHash',Types::STRING);
	}

	public static function buildHash(int $message_id, $filename, User $user): string {
		return md5($message_id . '|' . $filename . '|' . $user->getUID());
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'messageId' => $this->messageId,
			'name' => $this->name,
			'filetype' => $this->filetype,
			'height' => $this->height,
			'width' => $this->width,
			'url' => $this->url
		];
	}
}
