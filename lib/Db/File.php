<?php
namespace OCA\SmsBackupVault\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity,
    \OCP\DB\Types as Types;

class File extends Entity implements JsonSerializable {
    protected $message_id;
    protected $filename;
    protected $file_type;

    public function __construct() {
        $this->addType('message_id',Types::INTEGER);
        $this->addType('filename',Types::STRING);
        $this->addType('file_type',Types::STRING);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'message_id' => $this->message_id,
            'filename' => $this->filename,
            'file_type' => $this->file_type
        ];
    }
}
