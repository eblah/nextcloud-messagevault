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
						:to="{name: 'thread', params: { threadId: thread.id }}">
						<template slot="actions">
							<ActionButton icon="icon-delete"
														@click="deleteThread(thread)">
								{{ t('messagevault', 'Delete Thread') }}
							</ActionButton>
						</template>
					</AppNavigationItem>
				</div>
				<AppNavigationCaption v-else
					:title="t('messagevault', 'No messages have been imported.')" />
			</template>
			<template #footer>
				<AppNavigationSettings>
					<SettingsConfig />
				</AppNavigationSettings>
			</template>
		</AppNavigation>
		<AppContent :class="{ 'icon-loading': loading }">
			<Thread v-if="activeThreadId"
							:id="activeThreadId"
							:key="activeThreadId"
							:start-page="activePageNumber" />
			<div v-else id="emptycontent">
				<div v-if="threadList.length || loading">
					<div class="icon-message-vault" />
					<h2>{{ t('messagevault', 'Select a message thread to view...') }}</h2>
				</div>
				<div v-else class="section">
					<div class="icon-message-vault" />
					<h2>{{ t('messagevault', 'Welcome Message Vault') }}</h2>
					<p>
						{{ t('messagevault', 'Message Vault currently supports backups created using the Android App') }}
						<a href="https://play.google.com/store/apps/details?id=com.riteshsahu.SMSBackupRestore&hl=en_US&gl=US" target="_blank">XML Backup and Restore</a>.
					</p>
					<h3>{{ t('messagevault', 'Getting Started') }}</h3>
					<p>
						{{ t('messagevault', 'To get started, go to Settings and add any addresses that you have used to send \
							messages. After that, you can import your first backup. The file will be imported on the next Nextcloud\
							job run, usually less than five minutes.') }}
					</p>
					<p>
						{{ t('messagevault', 'It could take many hours to import all messages, depending on the size of the backup \
						and if attachments were added to the backups.') }}
					</p>
					<h3>{{ t('messagevault', 'Next Steps') }}</h3>
					<p>
						{{ t('messagevault', 'After your initial import, you can use a workflow to automatically import new backups.\
						If a file is not recognized as XML it will not be imported.') }}
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
import Thread from './Thread';
import SettingsConfig from '../Settings/Config';
import SettingsImport from '../Settings/Import';
import AppNavigationCaption from '@nextcloud/vue/dist/Components/AppNavigationCaption';

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
		AppNavigationCaption,
		ActionButton,
		Thread,
		SettingsConfig,
		SettingsImport,
	},
	props: {
		threadId: [String, Number, null],
		pageNumber: [String, Number, null],
	},
	data() {
		return {
			threadList: [],
			loading: true,
			activeThreadId: null,
			activePageNumber: null,
		};
	},
	computed: {
	},

	watch: {
		$route(to, from) {
			if (to.name === 'thread' && to.params.threadId) {
				this.activeThreadId = parseInt(to.params.threadId);
				if (to.params.pageNumber) {
					this.activePageNumber = parseInt(to.params.pageNumber);
				} else {
					this.activePageNumber = null;
				}
			}
		},
	},

	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			await this.loadAddresses();

			const response = await axios.get(generateUrl('/apps/messagevault/thread'));
			this.threadList = this.compileAddress(response.data);

			if (this.threadId) this.activeThreadId = parseInt(this.threadId);
			if (this.pageNumber) this.activePageNumber = parseInt(this.pageNumber);
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
