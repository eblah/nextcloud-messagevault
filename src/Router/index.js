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
import Vue from 'vue';
import Router from 'vue-router';
import { generateUrl } from '@nextcloud/router';

import MessageVault from '../Views/MessageVault';
import Thread from '../Views/MessageVault';

Vue.use(Router);

export default new Router({
	mode: 'history',
	base: generateUrl('/apps/messagevault', ''),
	linkActiveClass: 'active',
	routes: [{
		path: '/',
		component: MessageVault,
		name: 'root',
		props: true,
		children: [
			{
				path: '/t/:threadId',
				name: 'thread',
				component: MessageVault,
			},
			{
				path: '/t/:threadId/p/:pageNumber',
				name: 'thread-page',
				component: MessageVault,
			},
			{
				path: '/t/:threadId/s/:searchTerm',
				name: 'thread-search',
				component: Thread,
			},
			{ /** @todo this needs cleaning up **/
				path: '/t/:threadId/s/',
				redirect: to => `/t/${this.params.threadId}`
			},
		],
	}],
});
