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
					<em>{{ t('messagevault', 'Backups do not always contain your mobile number as the outgoing address. \
						This can cause issues with double or incomplete messages since there is no way to accurately obtain your \
						address from the backup file.') }}</em><br>
					<em>{{ t('messagevault', 'Separate additional addresses by a comma, such as in cases with multiple \
						phone numbers from different phones or different SIMs. \
						Since addresses can be numbers or email addresses, anything can be accepted here.') }}</em>
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
