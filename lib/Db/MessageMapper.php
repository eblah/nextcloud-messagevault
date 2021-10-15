<?php

declare(strict_types=1);

/**
 * @copyright 2020 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @author Georg Ehrke <oc.list@georgehrke.com>
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
namespace OCA\MessageVault\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class MessageMapper extends QBMapper {
	public const TABLE_NAME = 'msgvault_message';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLE_NAME, Message::class);
	}

	/**
	 * @return Message[]
	 */
	public function findAll($thread_id, $page, $limit): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('id', 'm.address_id', 'm.timestamp', 'm.received', 'm.body')
			->from($this->getTableName(), 'm')
			->where(
				$qb->expr()->eq('thread_id', $qb->createNamedParameter($thread_id))
			)
			->orderBy('timestamp', 'desc')
			->setMaxResults($limit)
			->setFirstResult($page * $limit);

		return $this->findEntities($select);
	}

	/**
	 * @return Message[]
	 */
	public function findAllMessageIds($thread_id): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('id')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('thread_id', $qb->createNamedParameter($thread_id))
			);

		$result = $select->executeQuery();
		$messages = $result->fetchAll();
		$result->closeCursor();

		return array_column($messages, 'id');
	}

	public function getMessageCount(int $thread_id): int {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select($qb->createFunction('COUNT(*)'))
			->from($this->getTableName(), 'm')
			->where(
				$qb->expr()->eq('thread_id', $qb->createNamedParameter($thread_id))
			);
		$result = $select->executeQuery();
		$cnt = $result->fetchColumn();
		$result->closeCursor();

		return (int)$cnt;
	}

	public function doesHashExist($hash): ?int {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('id')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('unique_hash', $qb->createNamedParameter($hash))
			);

		$result = $select->execute();
		$cnt = $result->fetchColumn();
		$result->closeCursor();

		return $cnt !== false ? (int)$cnt : null;
	}

	/**
	 * @return Message
	 */
	public function find($id): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $id)
			);

		return $this->findEntities($select);
	}

	public function deleteThreadMessages(int $thread_id): void {
		$qb = $this->db->getQueryBuilder();
		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('thread_id', $qb->createNamedParameter($thread_id))
			);
		$qb->execute();
	}
}
