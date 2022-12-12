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
	private $thread_service;

	public function __construct($AppName, IRequest $request, MessageService $service,
								ThreadService $thread_service, AttachmentService $attachment_service, IUserSession $userSession) {
		parent::__construct($AppName, $request);
		$this->thread_service = $thread_service;
		$this->service = $service;
		$this->attachment_service = $attachment_service;
		$this->user = $userSession;
	}

	/**
     * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function index(int $thread_id, int $position = 0, $limit = 100, string $search = null): DataResponse {
		if(!$this->thread_service->hasPermission($this->user, $thread_id)) {
			throw new ForbiddenException('You do not have permission to view this message.');
		}

		$messages = $this->service->findAll($thread_id, $position, $limit, $search);

		if(count($messages)) {
			$attachments = $this->attachment_service->getAttachments($this->user, $thread_id, array_column($messages, 'id'));

			foreach($messages as $message) {
				$files = array_filter($attachments, function(Attachment $attachment) use ($message) {
					return ($message->getId() === $attachment->getMessageId());
				});

				if(count($files)) $message->setAttachments(array_values($files));
			}
		}

		return new DataResponse($messages);
	}
}
