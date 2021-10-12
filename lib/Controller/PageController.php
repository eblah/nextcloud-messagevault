<?php
namespace OCA\SmsBackupVault\Controller;

use OCA\SmsBackupVault\Cron\XmlImport;
use OCA\SmsBackupVault\Service\ImportXmlService;
use OCA\SmsBackupVault\Storage\AttachmentStorage;
use OCP\AppFramework\Http\DataResponse;
use OCP\BackgroundJob\IJobList;
use OCP\IConfig;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\IUserSession;
use OCP\Util;

class PageController extends Controller {
	private $config;
	private $user_session;
	private $job_list;

	private $import_xml;

	public function __construct($AppName, IRequest $request,
								ImportXmlService $import_xml,
								IConfig $config,
								IUserSession $user_session,
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
	 * @NoCSRFRequired
	 */
	public function import() {
		var_dump($this->job_list->add(XmlImport::class, [
			'uid' => $this->user_session->getUser()->getUID(),
			'xml_file' => '/full.xml',
		]));die;
		return;
		$this->import_xml
			->getFilePath($this->user_session->getUser(), '/sms.xml')
			->runImport();
		die('imported');
	}

	public function importAdd(string $filename) {
		//
		$this->job_list->add(XmlImport::class, [
			'uid' => $this->user_session->getUser()->getUID(),
			'xml_file' => $filename
		]);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function config() {
		$user = $this->user_session->getUser()->getUID();

		return new DataResponse([
			'myAddress' => $this->config->getUserValue($user, $this->appName, 'myAddress'),
			'backupDir' => $this->config->getUserValue($user, $this->appName, 'backupDir'),
		]);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function configSave(string $myAddress, string $backupDir) {
		$user = $this->user_session->getUser()->getUID();

		if($myAddress === '') {
			$this->config->deleteUserValue($user, $this->appName, 'myAddress');
		} else {
			$this->config->setUserValue($user, $this->appName, 'myAddress', $myAddress);
		}

		$this->config->setUserValue($user, $this->appName, 'backupDir', $backupDir);

		return new DataResponse([]);
	}
}
