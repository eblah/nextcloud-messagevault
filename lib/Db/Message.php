<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity,
    \OCP\DB\Types as Types;

class Message extends Entity implements JsonSerializable {
    protected $thread_id;
    protected $address_id;
    protected $timestamp;
    protected $send_rec;
    protected $subject;
    protected $body;

    public function __construct() {
        $this->addType('thread_id',Types::INTEGER);
        $this->addType('address_id',Types::INTEGER);
        $this->addType('timestamp',Types::INTEGER);
        $this->addType('send_rec',Types::INTEGER);
        $this->addType('subject',Types::STRING);
        $this->addType('body',Types::TEXT);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'thread_id' => $this->thread_id,
            'address_id' => $this->address_id,
            'timestamp' => $this->timestamp,
            'send_rec' => $this->send_rec,
            'subject' => $this->subject,
            'body' => $this->body
        ];
    }
}
