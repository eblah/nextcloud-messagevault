<?php
namespace OCA\MessageVault\Controller;

use OC\ForbiddenException;
use OCA\MessageVault\Service\AttachmentService;
use OCA\MessageVault\Service\MessageService;
use OCA\MessageVault\Service\ThreadService;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\IUserSession;
use Psr\Container\ContainerInterface;

class ThreadController extends Controller {
	private $user_session;
	private $service;
	private $container;

	public function __construct($AppName, IRequest $request, ThreadService $service, IUserSession $userSession,
								ContainerInterface $container) {
		parent::__construct($AppName, $request);
		$this->container = $container;
		$this->service = $service;
		$this->user_session = $userSession;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->user_session));
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return new DataResponse($this->service->getThreadDetails($this->user_session, $id));
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function destroy(int $id): DataResponse {
		$user = $this->user_session->getUser();

		if(!$this->service->hasPermission($this->user_session, $id)) {
			throw new ForbiddenException('You do not have permission to delete this message.');
		}

		/** @var MessageService $message_service */
		$message_service = $this->container->query(MessageService::class);
		/** @var AttachmentService $attachment_service */
		$attachment_service = $this->container->query(AttachmentService::class);

		$threads = $message_service->findAllMessageIds($id);
		$attachment_service->delete($user, $id, $threads);

		$message_service->deleteThreadMessages($id);
		$this->service->delete($id);

		return new DataResponse(true);
	}
}
