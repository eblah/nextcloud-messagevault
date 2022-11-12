<template>
  <NcAppNavigationItem :title="t('messagevault', 'Import a new backup file')"
    :disabled="false"
    button-class="icon-upload"
    @click="openPicker">
    <Upload slot="icon" :size="20" />
  </NcAppNavigationItem>
</template>

<script>
import { getFilePickerBuilder, showError, showSuccess } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import { NcAppNavigationItem } from '@nextcloud/vue';
import Upload from 'vue-material-design-icons/Upload.vue';

const picker = getFilePickerBuilder(t('messagevault', 'Choose a XML backup to import'))
	.setMultiSelect(false)
	.setModal(true)
	.setType(1)
	.allowDirectories(false)
	.setMimeTypeFilter(['application/xml', 'text/xml'])
	.build();

export default {
	name: 'Import',
	components: {
		NcAppNavigationItem,
    Upload,
	},
	methods: {
		async processLocalFile(path) {
			try {
				const response = await axios.post(generateUrl('/apps/messagevault/import/new'), {
					filename: path,
				});
				if (response.data) showSuccess(t('messagevault', 'This backup will be processed soon.'));
				else throw new Error();
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
	},
};
</script>
