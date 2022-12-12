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
namespace OCA\MessageVault\Service;

use OC\User\User;
use OCA\MessageVault\Db\AttachmentMapper;
use OCA\MessageVault\Storage\AttachmentStorage;
use OCP\Files\NotFoundException;
use OCP\IUserSession;

class AttachmentService {
	private $mapper;
	private $storage;

	public function __construct(AttachmentMapper $mapper, AttachmentStorage $storage) {
		$this->mapper = $mapper;
		$this->storage = $storage;
	}

	public function getAttachments(IUserSession $user_session, int $thread_id, array $message_ids = []): array {
		$attachments = $this->mapper->findAllByMessageId($message_ids);

		foreach($attachments as $file) {
			try {
				$file->setUrl($this->storage->getFileUrl($user_session->getUser(), $thread_id, $file->getId()));
			} catch(NotFoundException $e) {
				continue;
			}
		}

		return $attachments;
	}

	public function delete(User $user, int $thread_id, array $message_ids) {
		$this->storage->deleteThread($user, $thread_id);
		$this->mapper->deleteMessages($message_ids);
	}
}
