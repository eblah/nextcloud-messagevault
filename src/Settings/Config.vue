<template>
	<div>
		<button @click="openSettings = true">
			Settings
		</button>

		<AppSettingsDialog
				:open="openSettings"
				:show-navigation="true">
			<AppSettingsSection title="Import Settings">
				<input type="text" id="myAddress" class="app-settings"
							 v-model="config.myAddress">
				<label for="myAddress">{{ t('smsbackupvault', 'Your phone number(s)') }}</label>
				<em>{{ t('smsbackupvault', 'Backups do not always have your mobile number as the outgoing address. \
					Enter your phone number here to ensure they are excluded. Separate additional numbers by a comma.\
					Since addresses can be numbers or email addresses, nearly anything can be accepted here.') }}</em>
				<input type="text" id="backupDir" class="app-settings"
							 v-model="config.backupDir">
				<label for="backupDir">{{ t('smsbackupvault', 'Backup folder') }}</label>
				<em>{{ t('smsbackupvault', 'Path to where your XML backups are being uploaded from your mobile device.') }}</em>
				<br>
				<button @click="saveSettings">Save</button>
			</AppSettingsSection>
		</AppSettingsDialog>
	</div>
</template>

<script>
import AppSettingsDialog from '@nextcloud/vue/dist/Components/AppSettingsDialog';
import AppSettingsSection from '@nextcloud/vue/dist/Components/AppSettingsSection';
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton';

import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";

export default {
	name: "Config",
	components: {
		AppSettingsDialog,
		AppSettingsSection,
		ActionButton,
	},
	props: [],
	data() {
		return {
			config: null,
			openSettings: false,
		};
	},
	/** @todo only load this if called upon */
	async mounted() {
		const response = await axios.get(generateUrl('/apps/smsbackupvault/config'));
		this.config = response.data;
	},
	methods: {
		async saveSettings() {
			const response = await axios.post(generateUrl('/apps/smsbackupvault/config/save'), this.config);
			this.openSettings = false;
		},
	},
	computed: {
	}
}
</script>

<style scoped>
input.app-settings {
	width: 100%;
}
</style>