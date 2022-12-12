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

class Message extends Entity implements JsonSerializable {
	protected $threadId;
	protected $addressId;
	protected $timestamp;
	protected $received;
	protected $body;
	protected $uniqueHash;

	private $attachments;

	public function __construct() {
		$this->addType('threadId',Types::INTEGER);
		$this->addType('addressId',Types::INTEGER);
		$this->addType('timestamp',Types::INTEGER);
		$this->addType('received',Types::INTEGER);
		$this->addType('body',Types::STRING);
		$this->addType('uniqueHash',Types::STRING);
	}

	public static function buildHash(int $address_id, int $date, string $body = null): string {
		return md5($address_id . '|' . $date . '|' . $body);
	}

	public function setAttachments($attachments): Message {
		$this->attachments = $attachments;
		return $this;
	}

	public function jsonSerialize() {
		$return = [
			'id' => $this->id,
			'addressId' => $this->addressId,
			'timestamp' => $this->timestamp,
			'received' => $this->received,
			'body' => $this->body,
			'attachments' => $this->attachments,
		];

		return $return;
	}
}
