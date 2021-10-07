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
namespace OCA\SmsBackupVault\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\IUser;

class MessageMapper extends QBMapper {
    public const TABLE_NAME = 'sms_message';

    public function __construct(IDBConnection $db) {
        parent::__construct($db, self::TABLE_NAME, Message::class);
    }

    /**
     * @return Message[]
     */
    public function findAll($thread_id, $page, $limit): array {
        $qb = $this->db->getQueryBuilder();

        $select = $qb->select('a.address', 'a.name', 'm.timestamp', 'm.received', 'm.body')
            ->from($this->getTableName(), 'm')
            ->where(
                $qb->expr()->eq('thread_id', $qb->createNamedParameter($thread_id))
            )
            ->join('m', 'sms_address', 'a', 'm.address_id = a.id')
            ->orderBy('timestamp', 'desc')
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit);

        $result = $select->executeQuery();
        $return = $result->fetchAll();
        $result->closeCursor();

        return $return;
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
}
