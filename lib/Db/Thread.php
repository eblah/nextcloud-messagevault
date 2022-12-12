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
namespace OCA\MessageVault\Db;

use JsonSerializable;

use OC\User\User;
use OCP\AppFramework\Db\Entity;
use OCP\DB\Types as Types;

class Thread extends Entity implements JsonSerializable {
	protected $userId;
	protected $name;
	protected $uniqueHash;

	public function __construct() {
		$this->addType('userId',Types::STRING);
		$this->addType('name',Types::STRING);
		$this->addType('uniqueHash',Types::STRING);
	}

	public static function buildHash(array $address_ids, User $user): string {
		sort($address_ids);
		return md5(implode(',', $address_ids) . '|' . $user->getUID());
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}
}
