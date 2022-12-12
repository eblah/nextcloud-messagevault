<?php

declare(strict_types=1);

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
namespace OCA\MessageVault\Db;

use OC\User\User;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\IDBConnection;

class ThreadMapper extends QBMapper {
	public const TABLE_NAME = 'msgvault_thread';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLE_NAME, Thread::class);
	}

	/**
	 * @return Thread[]
	 */
	public function findAll(User $user): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('t.id', 't.name')
			->from($this->getTableName(), 't')
			->where($qb->expr()->eq('t.user_id', $qb->createNamedParameter($user->getUID())))
			->orderBy('t.name');

		$result = $select->executeQuery();
		$threads = [];
		while($row = $result->fetch()) {
			$row['id'] = (int)$row['id'];
			$threads[] = $row;
		}
		$result->closeCursor();

		return $threads;
	}

	public function findHash(string $hash_idx, User $user): ?int {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('id')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID()))
			)->andWhere(
				$qb->expr()->eq('unique_hash', $qb->createNamedParameter($hash_idx))
			);

		try {
			return $this->findEntity($select)
				->getId();
		} catch (DoesNotExistException $e) { // can't be multiple exception due to being unique
			return null;
		} catch (Exception $e) {
			return null;
		}
	}

	public function findAllHashes(User $user): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('id', 'unique_hash')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())));

		return $this->findEntities($select);
	}

	/**
	 * @return Thread
	 */
	public function find($id, User $user): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID()))
			)->andWhere(
				$qb->expr()->eq('id', $qb->createNamedParameter($id))
			);

		return $this->findEntities($select);
	}

	public function insert(Entity $entity): Entity {
		return parent::insert($entity);
	}
}
