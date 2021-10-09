<template>
	<div id="content" class="app-smsbackupvault">
		<AppNavigation :class="{ 'icon-loading': loading }">
			<template #list>
					<AppNavigationItem v-for="thread in threadList"
						:key="thread.id"
						:title="thread.name"
						:class="{active: activeThreadId === thread.id}"
						@click="activeThreadId = thread.id">
					</AppNavigationItem>
			</template>
			<template #footer>
				<AppNavigationSettings :title="t('smsbackupvault', 'Settings')">
				</AppNavigationSettings>
			</template>
		</AppNavigation>
		<AppContent :class="{ 'icon-loading': loading }">
			<Thread v-if="activeThreadId" :id="activeThreadId" :key="activeThreadId"></Thread>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('smsbackupvault', 'Select a message thread to view...') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton';
import AppContent from '@nextcloud/vue/dist/Components/AppContent';
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation';
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem';
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew';
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings';
import Thread from './Views/Thread';

import '@nextcloud/dialogs/styles/toast.scss';
import { generateUrl } from '@nextcloud/router';
import { showError, showSuccess } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';

export default {
	name: 'SmsBackupVault',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
		AppNavigationSettings,
		Thread,
	},
	data() {
		return {
			threadList: [],
			loading: true,
			activeThreadId: null
		};
	},
	computed: {
	},

	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/smsbackupvault/thread'));
			this.threadList = response.data;
		} catch (e) {
			console.error(e);
			showError(t(this.name, 'Could not load threads.'));
		}
		this.loading = false;
	},

	methods: {
		async deleteThread(thread) {

		},
	},
};
</script>
