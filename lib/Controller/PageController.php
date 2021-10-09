<?php
namespace OCA\SmsBackupVault\Controller;

use OCA\SmsBackupVault\Service\ImportXmlService;
use OCA\SmsBackupVault\Storage\AttachmentStorage;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\Util;

class PageController extends Controller {
	private $userId;

	private $import_xml;

	public function __construct($AppName, IRequest $request, ImportXmlService $import_xml, $UserId) {
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->import_xml = $import_xml;
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
		$this->import_xml
			->runImport();
		die('imported');
	}
}
