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
return [
	'resources' => [
		'thread' => ['url' => '/thread'],
		'message' => ['url' => '/thread/{thread_id}/messages'],
		'address' => ['url' => '/address'],
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'page#index', 'url' => '/t/{thread_id}', 'verb' => 'GET', 'postfix' => 'thread'],
        ['name' => 'page#index', 'url' => '/t/{thread_id}/p/{page_number}', 'verb' => 'GET', 'postfix' => 'thread-page'],
        ['name' => 'page#index', 'url' => '/t/{thread_id}/s/{search_term}', 'verb' => 'GET', 'postfix' => 'thread-page'],
		['name' => 'page#config', 'url' => '/config', 'verb' => 'GET'],
		['name' => 'page#configSave', 'url' => '/config/save', 'verb' => 'POST'],
		['name' => 'page#import', 'url' => '/import', 'verb' => 'GET'],
		['name' => 'page#importAdd', 'url' => '/import/new', 'verb' => 'POST'],
	]
];
