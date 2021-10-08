<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity,
    \OCP\DB\Types as Types;

class Attachment extends Entity implements JsonSerializable {
    protected $messageId;
    protected $name;
    protected $filetype;
    protected $uniqueHash;

    protected $url;

    public function __construct() {
        $this->addType('messageId',Types::INTEGER);
        $this->addType('name',Types::STRING);
        $this->addType('filetype',Types::STRING);
        $this->addType('uniqueHash',Types::STRING);
    }

    public static function buildHash(int $message_id, $filename, $user_id): string {
        return md5($message_id . '|' . $filename . '|' . $user_id);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'messageId' => $this->messageId,
            'name' => $this->name,
            'filetype' => $this->filetype,
            'url' => $this->url
        ];
    }
}
