<?php
namespace OCA\SmsBackupVault\Controller;

use OCA\SmsBackupVault\Service\ImportXmlService;
use OCA\SmsBackupVault\Service\ThreadService;
use OCA\SmsBackupVault\Storage\AttachmentStorage;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\IUserSession;
use OCP\Util;

class ThreadController extends Controller {
	private $user_session;
	private $service;

	public function __construct($AppName, IRequest $request, ThreadService $service, IUserSession $userSession) {
		parent::__construct($AppName, $request);
		$this->service = $service;
		$this->user_session = $userSession;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->user_session));
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $id): DataResponse {
		return new DataResponse($this->service->getThreadDetails($this->user_session, $id));
	}
}
