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
import { showError, showSuccess } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';

const picker = getFilePickerBuilder(t('messagevault', 'Choose an XML backup to import'))
	.setMultiSelect(false)
	.setModal(true)
	.setType(1)
	.allowDirectories(false)
	.setMimeTypeFilter(['application/xml', 'text/xml'])
	.build();

export default {
	name: 'Import',
	methods: {
		async processLocalFile(path) {
			try {
				const response = await axios.post(generateUrl('/apps/messagevault/import/new'), {
					filename: path
				});
				if(response.data) showSuccess(t('messagevault', 'This backup will be processed soon.'));
					else throw false;
			} catch (e) {
				showError('There was an error adding this import.');
			}
		},

		/**
		 * Open nextcloud file picker
		 */
		async openPicker() {
			try {
				this.loading = true;
				const path = await picker.pick();
				await this.processLocalFile(path);
			} catch (error) {
				console.error('Could not pick file.', error);
			} finally {
				this.loading = false;
			}
		},
	}
};
</script>

<style scoped>

</style>