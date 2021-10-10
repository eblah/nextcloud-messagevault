<template>
	<div class="message-item" :class="{'message-sent': received == 0}">
		{{ formattedDate }}, {{ addressId }}<br />
		{{ body }}
		<div v-for="file in attachments">
			<img v-if="file.filetype.match('image')" :src="file.url" :height="file.height" :width="file.width" style="max-width: 400px; height: auto;">
			<video v-else-if="file.filetype.match('video')" width="400" controls>
				<source :src="file.url" :type="file.filetype">
			</video>
		</div>
	</div>
</template>

<script>
import moment from '@nextcloud/moment';

export default {
	name: "MessageItem",
	props: {
		body: String|null,
		received: Number,
		timestamp: Number,
		addressId: Number|null,
		attachments: Array|null
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
}
</script>

<style scoped>
.message-item {
	margin: 10px 25% 10px 0;
	padding: 10px;
	background: #ddd;
}

.message-item.message-sent {
	margin: auto 0 auto 25%;
	text-align: right;
}
</style>