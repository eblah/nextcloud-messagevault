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

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class ThreadAddressMapper extends QBMapper {
	public const TABLE_NAME = 'msgvault_thread_address';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLE_NAME, ThreadAddress::class);
	}

	/**
	 * @return ThreadAddress[]
	 */
	public function findAll(): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('*')
			->from($this->getTableName());

		return $this->findEntities($select);
	}

	/**
	 * @return ThreadAddress[]
	 */
	public function findAllAddresses(int $thread_id): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('address_id')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('thread_id', $qb->createNamedParameter($thread_id))
			);
		$result = $select->executeQuery();

		$addrs = [];
		foreach($result->fetchAll() as $a) $addrs[] = (int)$a['address_id'];

		$result->closeCursor();


		return $addrs;
	}

	public function deleteThread(int $thread_id) {
		$qb = $this->db->getQueryBuilder();
		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('thread_id', $qb->createNamedParameter($thread_id))
			);
		$qb->execute();
	}
}
