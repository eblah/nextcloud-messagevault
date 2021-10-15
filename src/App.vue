<template>
	<div id="content" class="app-messagevault">
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
				<AppNavigationSettings>
					<SettingsConfig />
					<SettingsImport />
				</AppNavigationSettings>
			</template>
		</AppNavigation>
		<AppContent :class="{ 'icon-loading': loading }">
			<Thread v-if="activeThreadId" :id="activeThreadId" :key="activeThreadId"></Thread>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('messagevault', 'Select a message thread to view...') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import AppContent from '@nextcloud/vue/dist/Components/AppContent';
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation';
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem';
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings';
import Thread from './Views/Thread';
import SettingsConfig from './Settings/Config';
import SettingsImport from './Settings/Import';

import '@nextcloud/dialogs/styles/toast.scss';
import { generateUrl } from '@nextcloud/router';
import { showError } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';

export default {
	name: 'messagevault',
	components: {
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationSettings,
		Thread,
		SettingsConfig,
		SettingsImport,
	},
	data() {
		return {
			threadList: [],
			loading: true,
		};
	},
	computed: {
	},

	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/messagevault/thread'));
			this.threadList = response.data;
		} catch (e) {
			console.error(e);
			showError(t(this.name, 'Could not load threads.'));
		}
		this.loading = false;
	},

	methods: {
		async deleteThread(thread) {
			return thread;
		},
	},
};
</script>
