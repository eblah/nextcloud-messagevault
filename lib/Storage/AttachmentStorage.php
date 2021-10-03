<?php
namespace OCA\SmsBackupVault\Storage;

use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;

class AttachmentStorage {
    /** @var IRootStorage */
    private $storage;
    private $user_id;
    private $app_name;

    public function __construct(IRootFolder $storage, $UserId, $AppName) {
        $this->storage = $storage;
        $this->user_id = $UserId;
        $this->app_name = $AppName;
    }

    public function writeFile($thread_id, $attachment_id, $data) {
        $user_folder = $this->storage->getUserFolder($this->user_id);

        $folder = sprintf('.%s/%d', $this->app_name, $thread_id);

        try {
            $user_folder->get($folder);
        } catch (NotFoundException $e) {
            $user_folder->newFolder($folder);
        }

        $filename = $folder . '/' . $attachment_id;
        // check if file exists and write to it if possible
        try {
            if($user_folder->nodeExists($filename)) {
                throw new StorageException('File already exists.');
            }

            $user_folder->newFile($filename, $data);
        } catch(\OCP\Files\NotPermittedException $e) {
            throw new StorageException('Cant write attachment');
        }
    }
}
