<template>
	<div id="content" class="app-messagevault">
		<AppNavigation :class="{ 'icon-loading': loading }">
			<template #list>
					<AppNavigationItem v-for="thread in threadList"
						:key="thread.id"
						:title="thread.name"
						:class="{active: activeThreadId === thread.id}"
						@click="activeThreadId = thread.id">
						<template slot="actions">
							<ActionButton icon="icon-delete"
														@click="deleteThread(thread)">
								{{ t('messagevault', 'Delete Thread') }}
							</ActionButton>
						</template>
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
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton';
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings';
import Thread from './Views/Thread';
import SettingsConfig from './Settings/Config';
import SettingsImport from './Settings/Import';

import '@nextcloud/dialogs/styles/toast.scss';
import { generateUrl } from '@nextcloud/router';
import { showError } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';

export default {
	name: 'MessageVault',
	components: {
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationSettings,
		ActionButton,
		Thread,
		SettingsConfig,
		SettingsImport,
	},
	data() {
		return {
			threadList: [],
			loading: true,
			activeThreadId: null,
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
		async deleteThreadConfirm(confirm) {
			if(!confirm) return;

			await axios.delete(generateUrl('/apps/messagevault/thread/' + this.activeThreadId));

			const idx = this.threadList.findIndex(i => i.id === this.activeThreadId);
			this.$delete(this.threadList, idx);
			this.activeThreadId = null;
		},

		deleteThread(thread) {
			this.activeThreadId = thread.id;

			OC.dialogs.confirm(
					t('messagevault', `This will delete the thread "${thread.name}". All messages and attachments associated with it will be removed. ` +
							'Are you sure you want to do that?'),
					t('messagevault', 'Delete thread?', { threadName: thread.name }),
					this.deleteThreadConfirm,
					true
			);
		},
	},
};
</script>
