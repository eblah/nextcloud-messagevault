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
namespace OCA\MessageVault\Cron;

use OCA\MessageVault\Service\ImportXmlService;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\QueuedJob;
use OCP\IUserManager;

class XmlImport extends QueuedJob {
	private $service;
	private $user_manager;

	public function __construct(ITimeFactory $time,
								ImportXmlService $service,
								IUserManager $user_manager) {
		parent::__construct($time);
		$this->user_manager = $user_manager;
		$this->service = $service;
	}

	protected function run($argument) {
		if(($user = $this->user_manager->get($argument['uid'])) !== null) {
			$this->service->runImport($user, $argument['file_id']);
		}
	}
}
