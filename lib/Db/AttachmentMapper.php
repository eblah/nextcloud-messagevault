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

class AttachmentMapper extends QBMapper {
	public const TABLE_NAME = 'sms_attachment';

	public function __construct(IDBConnection $db) {
		parent::__construct($db, self::TABLE_NAME, Attachment::class);
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
	 * @return Attachment
	 */
	public function find(int $id): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $id)
			);

		return $this->findEntities($select);
	}

	public function findAllByMessageId(array $message_ids): array {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('id', 'message_id', 'name', 'filetype', 'height', 'width')
			->from($this->getTableName())
			->where(
				$qb->expr()->in('message_id', $qb->createNamedParameter($message_ids, $qb::PARAM_INT_ARRAY))
			);

		return $this->findEntities($select);
	}
}
