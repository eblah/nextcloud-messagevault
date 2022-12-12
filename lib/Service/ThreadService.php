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

use OCA\MessageVault\Db\MessageMapper;
use OCA\MessageVault\Db\Thread;
use OCA\MessageVault\Db\ThreadAddress;
use OCA\MessageVault\Db\ThreadAddressMapper;
use OCA\MessageVault\Db\ThreadMapper;
use OCP\IUserSession;

class ThreadService {
	private $thread_mapper;
	private $thread_address_mapper;
	private $message_mapper;

	public function __construct(ThreadMapper $thread_mapper, ThreadAddressMapper $thread_address_mapper,
								MessageMapper $message_mapper) {
		$this->thread_address_mapper = $thread_address_mapper;
		$this->thread_mapper = $thread_mapper;
		$this->message_mapper = $message_mapper;
	}

	public function findAll(IUserSession $user): array {
		$threads = $this->thread_mapper->findAll($user->getUser());

		$address_ids = $this->thread_address_mapper->findAddressesByThreadIds(
			array_column($threads, 'id')
		);

		foreach($threads as &$thread) {
			$thread['a'] = $address_ids[$thread['id']];
		}
		unset($thread);
		return $threads;
	}

	/**
	 * Verifies the user has permission to read this thread
	 * @param string $user_id
	 * @return bool
	 */
	public function hasPermission(IUserSession $user, int $id): bool {
		$info = $this->thread_mapper->find($id, $user->getUser());
		return !empty($info);
	}

	public function getThreadDetails(IUserSession $user, int $id, string $search = null): array {
		$details = $this->thread_mapper->find($id, $user->getUser());
		if(empty($details)) return [];

		return [
			'id' => $details[0]->getId(),
			'name' => $details[0]->getName(),
			'addressIds' => $this->thread_address_mapper->findAllAddresses($id),
			'total' => $this->message_mapper->getMessageCount($id, $search)
		];
	}

	public function delete(int $thread_id): void {
		$this->thread_address_mapper->deleteThread($thread_id);

		$thread = new Thread();
		$thread->setId($thread_id);
		$this->thread_mapper->delete($thread);
	}
}
