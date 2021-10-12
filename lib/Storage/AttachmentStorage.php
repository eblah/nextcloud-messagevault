<?php
namespace OCA\SmsBackupVault\Storage;

use OC\User\User;
use OCP\Files\AlreadyExistsException;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\IURLGenerator;

class AttachmentStorage {
	/** @var IRootStorage */
	private $storage;
	private $app_name;
	private $url_generator;

	public function __construct(IRootFolder $storage, $AppName,
								IURLGenerator $url_generator) {
		$this->storage = $storage;
		$this->app_name = $AppName;
		$this->url_generator = $url_generator;
		$this->app_folder = '.' . $this->app_name;
	}

	private function getThreadPath($thread_id) {
		return $this->app_folder . '/' . $thread_id;;
	}

	public function getFileUrl(User $user, $thread_id, $attachment_id): ?string {
		$app_folder = $this->getOrCreateAppFolder($user);
		$attachment_path = $thread_id . '/' . $attachment_id;

		try {
			$app_folder->get($attachment_path);
			return $this->url_generator->getWebroot() . '/remote.php/webdav/' . $this->app_folder . '/' .
				$attachment_path;
		} catch (Exception $e) {
			return null;
		}
	}

	private function getOrCreateAppFolder(User $user): Node {
		$user_folder = $this->storage->getUserFolder($user->getUID());

		try {
			$folder = $user_folder->get($this->app_folder);
		} catch(NotFoundException $e) {
			$user_folder->newFolder($this->app_folder);
			$folder = $user_folder->get($this->app_folder);
			$folder->newFile('.nomedia');
			$folder->newFile('.nophoto');
		}

		return $folder;
	}

	/**
	 * Gets dimensions if a video or image
	 * @param $filename
	 * @param $filetype
	 * @return void
	 */
	public function getDimensions($filename, $filetype): ?array {
		if (strpos($filetype, 'image/') !== 0) return null;

		if(!($size = getimagesize($filename))) return null;
		return [$size[0], $size[1]];
	}

	public function writeFile(User $user, $thread_id, $attachment_id, $data) {
		$app_folder = $this->getOrCreateAppFolder($user);

		try {
			$app_folder->get($thread_id . '/');
		} catch (NotFoundException $e) {
			$app_folder->newFolder($thread_id);
		}

		$filename = $thread_id . '/' . $attachment_id;
		// check if file exists and write to it if possible
		try {
			if($app_folder->nodeExists($filename)) {
				throw new AlreadyExistsException('File already exists.');
			}

			$app_folder->newFile($filename, $data);
		} catch(\OCP\Files\NotPermittedException $e) {
			throw new StorageException('Cant write attachment');
		}
	}
}
