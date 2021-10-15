<?php

declare(strict_types=1);

namespace OCA\MessageVault\Workflow;

use OCA\MessageVault\Cron\XmlImport;
use OCA\WorkflowEngine\Entity\File;
use OCP\BackgroundJob\IJobList;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\GenericEvent;
use OCP\IL10N;
use OCP\WorkflowEngine\IManager;
use OCP\WorkflowEngine\IRuleMatcher;
use OCP\WorkflowEngine\ISpecificOperation;
use OCP\Files\FileInfo;
use OCP\Files\Node;
use OCP\IURLGenerator;
use Psr\Log\LoggerInterface;
use OCA\MessageVault\AppInfo\Application;

class ImportOperation implements ISpecificOperation {
	/** @var IJobList */
	private $job_list;
	/** @var IL10N */
	private $l;
	/** @var LoggerInterface */
	private $logger;
	/** @var IURLGenerator */
	private $url_generator;

	public function __construct(IJobList $job_list, IL10N $l, LoggerInterface $logger, IURLGenerator $url_generator) {
		$this->job_list = $job_list;
		$this->logger = $logger;
		$this->url_generator = $url_generator;
		$this->l = $l;
	}

	public function validateOperation(string $name, array $checks, string $operation): void {
	}

	public function getDisplayName(): string {
		return $this->l->t('XML SMS Backup');
	}

	public function getDescription(): string {
		return $this->l->t('Process XML SMS Backup file.');
	}

	public function getIcon(): string {
		return $this->url_generator->imagePath(Application::APP_ID, 'app.svg');
	}

	public function isAvailableForScope(int $scope): bool {
		return $scope === IManager::SCOPE_USER;
	}

	/**
	 * @param GenericEvent $event
	 */
	public function onEvent(string $eventName, Event $event, IRuleMatcher $rule_matcher): void {
		if(!$this->checkEvent($eventName, $event) || !$this->checkRuleMatcher($rule_matcher)) {
			return;
		}

		$node = $event->getSubject();
		if(!$node instanceof Node || $node->getType() !== FileInfo::TYPE_FILE) {
			return;
		}

		if(!in_array($node->getMimetype(), ['application/xml', 'text/xml'])) {
			return;
		}

		$this->logger->info('Adding new SMS import job for {filename} import for {user}.', [
			'filename' => $node->getPath(),
			'user'=> $node->getMimetype()
		]);
		$this->job_list->add(XmlImport::class, [
			'uid' => $node->getOwner()->getUID(),
			'file_id' => $node->getId()
		]);
	}

	public function getEntityId(): string {
		return File::class;
	}

	private function checkEvent(string $eventName, Event $event) : bool {
		if($eventName !== '\OCP\Files::postCreate' && $eventName !== '\OCP\Files::postWrite' ||
			!$event instanceof GenericEvent) {
			return false;
		}

		return true;
	}

	private function checkRuleMatcher(IRuleMatcher $ruleMatcher) : bool {
		$match = $ruleMatcher->getFlows(true);
		return !empty($match);
	}
}
