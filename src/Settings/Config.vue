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
    <NcAppSettingsDialog :open="open"
                         :show-navigation="false"
                         :title="t('messagevault', 'Message Vault settings')"
                         @update:open="saveSettings">
      <NcAppSettingsSection :title="t('messagevault', 'Import Settings')" id="import-settings">
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
      </NcAppSettingsSection>
    </NcAppSettingsDialog>
</template>

<script>
import { NcAppSettingsDialog, NcAppSettingsSection } from '@nextcloud/vue';

import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';

export default {
	name: 'Config',
	components: {
    NcAppSettingsDialog,
    NcAppSettingsSection,
	},
	props: {
    open: {
      type: Boolean,
      default: false,
    },
  },
	data() {
		return {
			config: {
				myAddress: null,
			},
			loading: false,
		};
	},
  watch: {
    async open(open_config) {
      if(open_config) {
        const response = await axios.get(generateUrl('/apps/messagevault/config'));
        this.config = response.data;
      } else {
        await axios.post(generateUrl('/apps/messagevault/config/save'), this.config);
      }
    },
  },
	methods: {
		async saveSettings() {
      this.$emit('update:open', false)
		},
	},
};
</script>

<style scoped>
input.app-settings {
	width: 50%;
}
</style>
