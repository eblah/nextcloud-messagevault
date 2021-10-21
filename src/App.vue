<template>
	<div id="content" class="app-messagevault">
		<AppNavigation :class="{ 'icon-loading': loading }">
			<SettingsImport />

			<template #list>
				<div v-if="threadList.length">
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
				</div>
				<AppNavigationCaption v-else :title="t('messagevault', 'No messages have been imported.')" />
			</template>
			<template #footer>
				<AppNavigationSettings>
					<SettingsConfig />
				</AppNavigationSettings>
			</template>
		</AppNavigation>
		<AppContent :class="{ 'icon-loading': loading }">
			<Thread v-if="activeThreadId" :id="activeThreadId" :key="activeThreadId" />
			<div v-else>
				<div v-if="threadList.length || loading" id="emptycontent">
					<div class="icon-file" />
					<h2>{{ t('messagevault', 'Select a message thread to view...') }}</h2>
				</div>
				<div v-else class="section">
					<h2>{{ t('messagevault', 'Getting Started ') }}</h2>
					<p>
						{{ t('messagevault', 'To get started, first go to Settings and add any addresses that you have used to send \
							messages. After that, import your first backup. The file will be imported on the next jobs run, usually about five minutes.') }}
					</p>
					<p>
						{{ t('messagevault', 'It could take several hours to import all messages, depending on the size of the backup.') }}
					</p>
					<p>
						{{ t('messagevault', 'You can use a workflow to automatically import backups into the system in the future. It is recommended to \
						watch a specific folder and import. If a file is not recognized as XML it will not be imported.') }}
					</p>
				</div>
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
import AppNavigationCaption from '@nextcloud/vue/dist/Components/AppNavigationCaption';

import '@nextcloud/dialogs/styles/toast.scss';
import { generateUrl } from '@nextcloud/router';
import { showError } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';

export default {
	name: 'App',
	components: {
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationSettings,
		AppNavigationCaption,
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
			await this.loadAddresses();

			const response = await axios.get(generateUrl('/apps/messagevault/thread'));
			this.threadList = this.compileAddress(response.data);
		} catch (e) {
			console.error(e);
			showError(t(this.name, 'Could not load threads.'));
		}
		this.loading = false;
	},

	methods: {
		compileAddress(response) {
			response.map(x => {
				if (x.name === null) {
					x.name = this.getThreadAddresses(x.a)
						.join(', ');
				}
				return x;
			});
			response.sort((a, b) => {
				const na = a.name.toUpperCase();
				const nb = b.name.toUpperCase();
				if (na < nb) return -1;
				if (na > nb) return 1;
				return 0;
			});

			return response;
		},

		async deleteThreadConfirm(confirm) {
			if (!confirm) return;

			await axios.delete(generateUrl('/apps/messagevault/thread/' + this.activeThreadId));

			const idx = this.threadList.findIndex(i => i.id === this.activeThreadId);
			this.$delete(this.threadList, idx);
			this.activeThreadId = null;
		},

		deleteThread(thread) {
			this.activeThreadId = thread.id;

			OC.dialogs.confirm(
				t('messagevault', `This will delete the thread "${thread.name}". All messages and attachments associated with it will be removed. `
						+ 'Are you sure you want to do that?'),
				t('messagevault', 'Delete thread?', { threadName: thread.name }),
				this.deleteThreadConfirm,
				true,
			);
		},
	},
};
</script>
