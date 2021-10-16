<template>
	<div class="message-item" :class="{'message-sent': received == 0, combined: combined}">
		<div v-if="body !== null" class="message-text">
			{{ body }}
		</div>
		<div class="attachment" v-else>
			<Attachment v-for="file in attachments"
									:key="file.id"
									:filetype="file.filetype"
									:url="file.url"
									:width="file.width"
									:height="file.height"
			/>
		</div>
		<div class="message-address">
			{{ formattedDate }} by {{ addressId }}
		</div>
	</div>
</template>

<script>
import moment from '@nextcloud/moment';
import Attachment from '../Components/Attachment'

export default {
	name: 'MessageItem',
	components: {
		Attachment
	},
	props: {
		body: String|null,
		received: Number,
		timestamp: Number,
		addressId: Number|null,
		attachments: Array|null,
		combined: Boolean,
	},
	data() {
		return {

		};
	},
	computed: {
		formattedDate() {
			return moment.unix(this.timestamp).format('LLLL');
		}
	}
};
</script>

<style lang="scss" scoped>
$message-recv-bg: #ddd;
$message-sent-bg: #aec4d6;

.message-item {
	display: flow-root;
}

.message-text {
	margin: 10px 25% 0px 0;
	padding: 10px;
	background-color: $message-recv-bg;
	float: left;
	border-radius: 10px;
}

.message-address {
	font-size: 9px;
	clear: both;
	float: left;
}

.message-sent {
	& .message-text {
		background-color: $message-sent-bg;
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