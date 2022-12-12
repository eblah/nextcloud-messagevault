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
     * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function show(int $id, string $search = null): DataResponse {
		return new DataResponse($this->service->getThreadDetails($this->user_session, $id, $search));
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
