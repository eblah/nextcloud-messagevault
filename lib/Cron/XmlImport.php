<?php
namespace OCA\SmsBackupVault\Cron;

use OCA\SmsBackupVault\Service\ImportXmlService;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\QueuedJob;
use OCP\IUserManager;

class XmlImport extends QueuedJob {
	private $service;
	private $user_manager;

	public function __construct(ITimeFactory $time,
								ImportXmlService $service,
								IUserManager $user_manager) {
		parent::__construct($time);
		$this->user_manager = $user_manager;
		$this->service = $service;
	}

	protected function run($argument) {
		if(($user = $this->user_manager->get($argument['uid'])) !== null) {
			$this->service->runImport($user, $argument['xml_file']);
		} else {
			var_dump($user);die;
		}
	}
}