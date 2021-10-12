<template>
	<div class="import-contact">
		<button class="import-contact__button-main" @click="openPicker">
			<span class="icon-upload" />
			{{ t('contacts', 'Import a Backup') }}
		</button>
	</div>
</template>

<script>
import { getFilePickerBuilder } from '@nextcloud/dialogs';

const picker = getFilePickerBuilder(t('smsbackupvault', 'Choose an XML backup to import'))
		.setMultiSelect(false)
		.setModal(true)
		.setType(1)
		.allowDirectories(false)
		.setMimeTypeFilter(['application/xml', 'text/xml'])
		.build();

export default {
	name: "Import",
	methods: {
		async processLocalFile(path) {

		},

		/**
		 * Open nextcloud file picker
		 */
		async openPicker() {
			try {
				this.loading = true
				const path = await picker.pick();
				await this.processLocalFile(path)
			} catch (error) {
				console.error('Could not pick file.', error)
			} finally {
				this.resetState()
			}
		},
	}
}
</script>

<style scoped>

</style>