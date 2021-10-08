<?php
namespace OCA\SmsBackupVault\Storage;

use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IURLGenerator;

class AttachmentStorage {
    /** @var IRootStorage */
    private $storage;
    private $user_id;
    private $app_name;
    private $url_generator;

    public function __construct(IRootFolder $storage, $UserId, $AppName,
                                IURLGenerator $url_generator) {
        $this->storage = $storage;
        $this->user_id = $UserId;
        $this->app_name = $AppName;
        $this->url_generator = $url_generator;
    }

    private function getThreadPath($thread_id) {
        return sprintf('.%s/%d', $this->app_name, $thread_id);;
    }

    public function getFileUrl($thread_id, $attachment_id): ?string {
        $user_folder = $this->storage->getUserFolder($this->user_id);
        $attachment_path = $this->getThreadPath($thread_id) . '/' . $attachment_id;

        try {
            $file = $user_folder->get($attachment_path);

            return $this->url_generator->getWebroot() . '/remote.php/webdav/' . $attachment_path;
        } catch (Exception $e) {
            return null;
        }
    }

    public function writeFile($thread_id, $attachment_id, $data) {
        $user_folder = $this->storage->getUserFolder($this->user_id);

        $folder = $this->getThreadPath($thread_id);

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
