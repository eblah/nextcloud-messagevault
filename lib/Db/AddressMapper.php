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

use OC\User\User;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\IUser;

class AddressMapper extends QBMapper {
    public const TABLE_NAME = 'sms_address';

    public function __construct(IDBConnection $db) {
        parent::__construct($db, self::TABLE_NAME, Address::class);
    }

    /**
     * @return Address[]
     */
    public function findAll(User $user, array $fields = ['*']): array {
        $qb = $this->db->getQueryBuilder();

        $select = $qb->select($fields)
            ->from($this->getTableName())
            ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())));

        return $this->findEntities($select);
    }

    /**
     * @return Address
     */
    public function find($id, User $user): array {
        $qb = $this->db->getQueryBuilder();

        $select = $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID()))
            )->andWhere(
                $qb->expr()->eq('id', $id)
            );

        return $this->findEntities($select);
    }
}
