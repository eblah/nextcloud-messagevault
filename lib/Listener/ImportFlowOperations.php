<?php

declare(strict_types=1);

namespace OCA\SmsBackupVault\Listener;

use OCA\SmsBackupVault\AppInfo\Application;
use OCA\SmsBackupVault\Workflow\ImportOperation;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;
use OCP\WorkflowEngine\Events\RegisterOperationsEvent;
use Psr\Container\ContainerInterface;

class ImportFlowOperations implements IEventListener {
	/** @var ContainerInterface */
	private $container;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	public function handle(Event $event): void {
		if (!$event instanceof RegisterOperationsEvent) {
			return;
		}
		$event->registerOperation($this->container->get(ImportOperation::class));
		Util::addScript(Application::APP_ID, 'admin');
	}
}
