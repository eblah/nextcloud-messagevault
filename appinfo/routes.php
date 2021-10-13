<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\SmsBackupVault\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
	'resources' => [
		'thread' => ['url' => '/thread'],
		'message' => ['url' => '/thread/{thread_id}/messages'],
		'address' => ['url' => '/address'],
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'page#config', 'url' => '/config', 'verb' => 'GET'],
		['name' => 'page#configSave', 'url' => '/config/save', 'verb' => 'POST'],
		['name' => 'page#import', 'url' => '/import', 'verb' => 'GET'],
		['name' => 'page#importAdd', 'url' => '/import/new', 'verb' => 'POST'],
	]
];
