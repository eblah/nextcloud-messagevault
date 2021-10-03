<?php
namespace OCA\SmsBackupVault\Controller;

use OCA\SmsBackupVault\Service\ImportXmlService;
use OCA\SmsBackupVault\Storage\AttachmentStorage;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;

class PageController extends Controller {
	private $userId;

    private $import_xml;

	public function __construct($AppName, IRequest $request, ImportXmlService $import_xml, $UserId) {
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
        $this->import_xml = $import_xml;
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
        $this->import_xml
            ->runImport();

        die;

		return new TemplateResponse('smsbackupvault', 'index');  // templates/index.php
	}

}
