<?php
namespace OCA\SmsBackupVault\Controller;

use OCA\SmsBackupVault\Service\ImportXmlService;
use OCA\SmsBackupVault\Service\ThreadService;
use OCA\SmsBackupVault\Storage\AttachmentStorage;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\Util;

class ThreadController extends Controller {
    private $user_id;
    private $service;

    private $import_xml;

    public function __construct($AppName, IRequest $request, ThreadService $service, $UserId) {
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->user_id = $UserId;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): DataResponse {
        return new DataResponse($this->service->findAll($this->user_id));
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(int $id, int $limit = 100, int $page = 0): DataResponse {
        return new DataResponse($this->service->loadThread($this->user_id, $id, $page, $limit));
    }
}
