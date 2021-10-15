<?php
namespace OCA\MessageVault\Controller;

use OCA\MessageVault\Service\ImportXmlService;
use OCA\MessageVault\Service\AddressService;
use OCA\MessageVault\Storage\AttachmentStorage;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\IUserSession;

class AddressController extends Controller {
    private $user_session;
    private $service;

    public function __construct($AppName, IRequest $request, AddressService $service, IUserSession $userSession) {
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
}
