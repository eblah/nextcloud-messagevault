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

class Attachment extends Entity implements JsonSerializable {
	protected $messageId;
	protected $name;
	protected $filetype;
	protected $uniqueHash;
	protected $width;
	protected $height;

	protected $url;

	public function __construct() {
		$this->addType('messageId',Types::INTEGER);
		$this->addType('name',Types::STRING);
		$this->addType('filetype',Types::STRING);
		$this->addType('width',Types::INTEGER);
		$this->addType('height',Types::INTEGER);
		$this->addType('uniqueHash',Types::STRING);
	}

	public static function buildHash(int $message_id, $filename, User $user): string {
		return md5($message_id . '|' . $filename . '|' . $user->getUID());
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'messageId' => $this->messageId,
			'name' => $this->name,
			'filetype' => $this->filetype,
			'height' => $this->height,
			'width' => $this->width,
			'url' => $this->url
		];
	}
}
