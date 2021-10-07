<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity,
    \OCP\DB\Types as Types;

class Message extends Entity implements JsonSerializable {
    protected $threadId;
    protected $addressId;
    protected $timestamp;
    protected $received;
    protected $subject;
    protected $body;
    protected $uniqueHash;

    public function __construct() {
        $this->addType('threadId',Types::INTEGER);
        $this->addType('addressId',Types::INTEGER);
        $this->addType('timestamp',Types::INTEGER);
        $this->addType('received',Types::INTEGER);
        $this->addType('subject',Types::STRING);
        $this->addType('body',Types::STRING);
        $this->addType('uniqueHash',Types::STRING);
    }

    public static function buildHash(int $address_id, int $date, string $subject = null, string $body = null): string {
        return md5($address_id . '|' . $date . '|' . $body);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'threadId' => $this->threadId,
            'addressId' => $this->addressId,
            'timestamp' => $this->timestamp,
            'received' => $this->received,
            'subject' => $this->subject,
            'body' => $this->body,
        ];
    }
}
