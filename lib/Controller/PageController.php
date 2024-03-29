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
namespace OCA\MessageVault\Controller;

use OCA\MessageVault\Cron\XmlImport;
use OCA\MessageVault\Service\ImportXmlService;
use OCP\AppFramework\Http\DataResponse;
use OCP\BackgroundJob\IJobList;
use OCP\IConfig;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\IUserSession;
use OCP\Util;
use Psr\Log\LoggerInterface;

class PageController extends Controller {
	private $config;
	private $user_session;
	private $job_list;

	private $import_xml;

	public function __construct($AppName, IRequest $request,
								ImportXmlService $import_xml,
								IConfig $config,
								IUserSession $user_session,
								LoggerInterface $log,
								IJobList $job_list) {
		parent::__construct($AppName, $request);
		$this->import_xml = $import_xml;
		$this->user_session = $user_session;
		$this->config = $config;
		$this->job_list = $job_list;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		Util::addScript('messagevault', 'messagevault-main');

		return new TemplateResponse('messagevault', 'index');
	}

	/**
	 * @NoAdminRequired
	 */
	public function importAdd(string $filename): DataResponse {
		$file_id = $this->import_xml->getNodeFromFilename($this->user_session->getUser(), $filename);

		if($file_id !== null) {
			$this->job_list->add(XmlImport::class, [
				'uid' => $this->user_session->getUser()->getUID(),
				'file_id' => $file_id->getId()
			]);
		}

		return new DataResponse(true);
	}

	/**
	 * @NoAdminRequired
	 */
	public function config(): DataResponse {
		$user = $this->user_session->getUser()->getUID();

		return new DataResponse([
			'myAddress' => $this->config->getUserValue($user, $this->appName, 'myAddress'),
			'backupDir' => $this->config->getUserValue($user, $this->appName, 'backupDir'),
		]);
	}

	/**
	 * @NoAdminRequired
	 */
	public function configSave(string $myAddress, string $backupDir) {
		$user = $this->user_session->getUser()->getUID();

		if($myAddress === '') {
			$this->config->deleteUserValue($user, $this->appName, 'myAddress');
		} else {
			$this->config->setUserValue($user, $this->appName, 'myAddress', $myAddress);
		}

		if($backupDir === '') {
			$this->config->deleteUserValue($user, $this->appName, 'backupDir');
		} else {
			$this->config->setUserValue($user, $this->appName, 'backupDir', $backupDir);
		}

		return new DataResponse([]);
	}
}
