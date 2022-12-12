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
	<div class="message-item" :class="{'message-sent': received === 0, combined: combined}">
		<div v-if="body !== null" class="message-text">
			{{ body }}
		</div>
		<div v-if="attachments" class="attachment">
			<Attachment v-for="file in attachments"
									:key="file.id"
									:filetype="file.filetype"
									:url="file.url"
									:name="file.name"
									:width="file.width"
									:height="file.height" />
		</div>
		<div class="message-address">
			<div v-if="received === 1">
				{{ t('messagevault', '{date} by {name}', { date: formattedDate, name: addressName }) }}
			</div>
			<div v-else>
				{{ formattedDate }}
			</div>
		</div>
	</div>
</template>

<script>
import moment from '@nextcloud/moment';
import Attachment from '../Components/Attachment';

export default {
	name: 'MessageItem',
	components: {
		Attachment,
	},
	props: {
		body: [String, null],
		received: Number,
		timestamp: Number,
		addressId: [Number, null],
		attachments: Array,
		combined: Boolean,
	},
	data() {
		return {

		};
	},
	computed: {
		formattedDate() {
			return moment.unix(this.timestamp).format('LLLL');
		},

		addressName() {
			const add = this.getAddressById(this.addressId);
			return add.name ?? add.address;
		},
	},
};
</script>

<style lang="scss" scoped>
$message-recv-bg: #ddd;
$message-sent-bg: #aec4d6;

$message-recv-bg-dark: #323232;
$message-sent-bg-dark: #424242;

.message-item {
	display: flow-root;
}

.message-text {
	margin: 10px 25% 0px 0;
	padding: 10px;
	background-color: $message-recv-bg;
	float: left;
	border-radius: 10px;

  @media (prefers-color-scheme: dark) {
    background-color: $message-recv-bg-dark;
  }
}

.message-address {
	font-size: 9px;
	clear: both;
	float: left;
}

.message-sent {
	& .message-text {
		background-color: $message-sent-bg;

    @media (prefers-color-scheme: dark) {
      background-color: $message-sent-bg-dark;
    }
	}

	& .message-address,
	& .message-text,
	& .attachment {
		margin: auto 0 auto 25%;
		text-align: right;
		float: right;
	}
}
</style>
