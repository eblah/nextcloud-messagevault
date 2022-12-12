<!--
 - @copyright Copyright (c) 2022 Justin Osborne <justin@eblah.com>
 -
 - @author Justin Osborne <justin@eblah.com>
 -
 - @license AGPL-3.0-or-later
 -
 - This program is free software: you can redistribute it and/or modify
 - it under the terms of the GNU Affero General Public License as
 - published by the Free Software Foundation, either version 3 of the
 - License, or (at your option) any later version.
 -
 - This program is distributed in the hope that it will be useful,
 - but WITHOUT ANY WARRANTY; without even the implied warranty of
 - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 - GNU Affero General Public License for more details.
 -
 - You should have received a copy of the GNU Affero General Public License
 - along with this program. If not, see <http://www.gnu.org/licenses/>.
 -
 -->
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
