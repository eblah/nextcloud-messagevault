<?php
namespace OCA\MessageVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;
use OCP\DB\Types as Types;

class Message extends Entity implements JsonSerializable {
	protected $threadId;
	protected $addressId;
	protected $timestamp;
	protected $received;
	protected $body;
	protected $uniqueHash;

	private $attachments;

	public function __construct() {
		$this->addType('threadId',Types::INTEGER);
		$this->addType('addressId',Types::INTEGER);
		$this->addType('timestamp',Types::INTEGER);
		$this->addType('received',Types::INTEGER);
		$this->addType('body',Types::STRING);
		$this->addType('uniqueHash',Types::STRING);
	}

	public static function buildHash(int $address_id, int $date, string $body = null): string {
		return md5($address_id . '|' . $date . '|' . $body);
	}

	public function setAttachments($attachments): Message {
		$this->attachments = $attachments;
		return $this;
	}

	public function jsonSerialize() {
		$return = [
			'id' => $this->id,
			'addressId' => $this->addressId,
			'timestamp' => $this->timestamp,
			'received' => $this->received,
			'body' => $this->body,
			'attachments' => $this->attachments,
		];

		return $return;
	}
}
