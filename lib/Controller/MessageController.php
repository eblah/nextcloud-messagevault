<?php
namespace OCA\MessageVault\Controller;

use OC\ForbiddenException;
use OCA\MessageVault\Db\Attachment;
use OCA\MessageVault\Service\AttachmentService;
use OCA\MessageVault\Service\ImportXmlService;
use OCA\MessageVault\Service\MessageService;
use OCA\MessageVault\Service\ThreadService;
use OCA\MessageVault\Storage\AttachmentStorage;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\IUserSession;

class MessageController extends Controller {
	private $user;
	private $service;
	private $attachment_service;

	public function __construct($AppName, IRequest $request, MessageService $service,
								ThreadService $thread_service, AttachmentService $attachment_service, IUserSession $userSession) {
		parent::__construct($AppName, $request);
		$this->thread_service = $thread_service;
		$this->service = $service;
		$this->attachment_service = $attachment_service;
		$this->user = $userSession;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(int $thread_id, int $position = 0, $limit = 100): DataResponse {
		if(!$this->thread_service->hasPermission($this->user, $thread_id)) {
			throw new ForbiddenException('You do not have permission to view this message.');
		}

		$messages = $this->service->findAll($thread_id, $position, $limit);

		if(count($messages)) {
			$attachments = $this->attachment_service->getAttachments($this->user, $thread_id, array_column($messages, 'id'));

			foreach($messages as $message) {
				$files = array_filter($attachments, function(Attachment $attachment) use ($message) {
					return ($message->getId() === $attachment->getMessageId());
				});

				if(count($files)) $message->setAttachments($files);
			}
		}

		return new DataResponse($messages);
	}
}
