import Vue from 'vue';
import Router from 'vue-router';
import { generateUrl } from '@nextcloud/router';

import MessageVault from '../Views/MessageVault';

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
		],
	}],
});
