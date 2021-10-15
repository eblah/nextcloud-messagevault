<?php

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
