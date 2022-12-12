<?php

/**
 * @copyright 2022 Justin Osborne <justin@eblah.com>
 * 
 * @author Justin Osborne <justin@eblah.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\MessageVault\Storage;

use OC\Files\Node\Folder;
use OC\User\User;
use OCP\Files\AlreadyExistsException;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\IURLGenerator;
use Sabre\DAV\Exception\NotFound;

class AttachmentStorage {
	/** @var IRootStorage */
	private $storage;
	/** @var string */
	private $app_name;
	/** @var IURLGenerator */
	private $url_generator;
	/** @var string  */
	private $app_folder;

	public function __construct(IRootFolder $storage, $AppName,
								IURLGenerator $url_generator) {
		$this->storage = $storage;
		$this->app_name = $AppName;
		$this->url_generator = $url_generator;
		$this->app_folder = '.' . $this->app_name;
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

	public function getLogFolder(User $user): Node {
		$app_folder = $this->getOrCreateAppFolder($user);
		try {
			$logs = $app_folder->get('logs');
		} catch(NotFoundException $e) {
			$app_folder->newFolder('logs');
			$logs = $app_folder->get('logs');
		}
		return $logs;
	}

	/**
	 * Gets dimensions if a video or image
	 * @param $filename
	 * @param $filetype
	 * @return void
	 */
	public function getDimensions($filename, $filetype): ?array {
		if(strpos($filetype, 'image/') !== 0) return null;

		if(!($size = getimagesize($filename))) return null;
		return [$size[0], $size[1]];
	}

	public function writeFile(User $user, $folder_name, $new_fn, $data) {
		$app_folder = $this->getOrCreateAppFolder($user);

		try {
			$app_folder->get($folder_name . '/');
		} catch (NotFoundException $e) {
			$app_folder->newFolder($folder_name);
		}

		$filename = $folder_name . '/' . $new_fn;
		// check if file exists and write to it if possible
		try {
			if($app_folder->nodeExists($filename)) {
				throw new AlreadyExistsException('File already exists.');
			}

			$app_folder->newFile($filename, $data);
		} catch(NotPermittedException $e) {
			throw new StorageException('Cant write attachment');
		}
	}

	public function deleteThread(User $user, $thread_id) {
		$app_folder = $this->getOrCreateAppFolder($user);

		try {
			/** @var Folder $folder */
			$folder = $app_folder->get($thread_id . '/');
			$folder->delete();
		} catch(NotFoundException $e) {
		}
	}
}
