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

class MessageService {
	private $message_mapper;

	public function __construct(MessageMapper $message_mapper) {
		$this->message_mapper = $message_mapper;
	}

	public function findAll(int $thread_id, int $page = 0, int $limit = 100, string $search = null) {
		return $this->message_mapper->findAll($thread_id, $page, $limit, $search);
	}

	public function findAllMessageIds(int $thread_id): array {
		return $this->message_mapper->findAllMessageIds($thread_id);
	}

	public function deleteThreadMessages(int $thread_id): void {
		$this->message_mapper->deleteThreadMessages($thread_id);
	}
}
