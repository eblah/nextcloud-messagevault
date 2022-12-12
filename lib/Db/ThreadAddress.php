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

use OCP\AppFramework\Db\Entity;
use OCP\DB\Types as Types;

class ThreadAddress extends Entity implements JsonSerializable {
	protected $threadId;
	protected $addressId;

	public function __construct() {
		$this->addType('threadId',Types::INTEGER);
		$this->addType('addressId',Types::INTEGER);
	}

	public function jsonSerialize() {
		return [
			'threadId' => $this->threadId,
			'addressId' => $this->addressId,
		];
	}
}
