<?php
namespace OCA\SmsBackupVault\Controller;

use OCA\SmsBackupVault\Cron\XmlImport;
use OCA\SmsBackupVault\Service\ImportXmlService;
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
		Util::addScript('smsbackupvault', 'smsbackupvault-main');

		return new TemplateResponse('smsbackupvault', 'index');  // templates/index.php
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
			$this->config->deleteUserValue($user, $this->appName, 'myAddress');
		} else {
			$this->config->setUserValue($user, $this->appName, 'backupDir', $backupDir);
		}

		return new DataResponse([]);
	}
}
