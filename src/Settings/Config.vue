<template>
	<div>
		<button @click="openSettingsDialog()">
			Settings
		</button>

		<AppSettingsDialog
				:open.sync="openSettings"
				:show-navigation="false"
				:class="{'icon-loading': loading}">
			<AppSettingsSection title="Import Settings">
				<div>
					<label for="myAddress">{{ t('messagevault', 'Your addresses') }}</label>
					<input id="myAddress"
								 v-model="config.myAddress"
								 type="text"
								 class="app-settings"><br>
					<em>{{ t('messagevault', 'Backups do not always have your mobile number as the outgoing address. \
						Enter your phone number here to ensure they are excluded. Separate additional numbers by a comma.\
						Since addresses can be numbers or email addresses, nearly anything can be accepted here.') }}</em>
				</div>
				<div>
					<label for="backupDir">{{ t('messagevault', 'Backup folder') }}</label>
					<input id="backupDir"
								 v-model="config.backupDir"
								 type="text"
								 class="app-settings">
					<button @click="chooseFolder">
						Choose
					</button>
					<br>
					<em>{{ t('messagevault', 'Path to where your XML backups are being uploaded from your mobile device.') }}</em>
				</div>
				<br>
				<button @click="saveSettings">
					Save
				</button>
			</AppSettingsSection>
		</AppSettingsDialog>
	</div>
</template>

<script>
import AppSettingsDialog from '@nextcloud/vue/dist/Components/AppSettingsDialog';
import AppSettingsSection from '@nextcloud/vue/dist/Components/AppSettingsSection';

import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import { getFilePickerBuilder } from '@nextcloud/dialogs';

const picker = getFilePickerBuilder(t('messagevault', 'Choose a folder to watch for new XML files'))
	.setMultiSelect(false)
	.setModal(true)
	.setType(1)
	.allowDirectories(true)
	.setMimeTypeFilter(['folder'])
	.build();

export default {
	name: 'Config',
	components: {
		AppSettingsDialog,
		AppSettingsSection,
	},
	props: [],
	data() {
		return {
			config: {
				myAddress: null,
				backupDir: null,
			},
			openSettings: false,
			loading: false,
		};
	},
	methods: {
		async openSettingsDialog() {
			this.loading = true;
			const response = await axios.get(generateUrl('/apps/messagevault/config'));
			this.config = response.data;
			this.loading = false;
			this.openSettings = true;
		},

		async chooseFolder() {
			try {
				const path = await picker.pick();
				this.config.backupDir = path;
			} catch (error) {
				console.error('Could not pick folder.', error);
			} finally {
				this.loading = false;
			}
		},

		async saveSettings() {
			this.loading = true;
			await axios.post(generateUrl('/apps/messagevault/config/save'), this.config);
			this.openSettings = false;
			this.loading = false;
		},
	},
};
</script>

<style scoped>
input.app-settings {
	width: 50%;
}
</style>
