<template>
	<div class="section">
		<div v-if="details">
			<h1>{{ details.name }}</h1>
			<div v-if="loading">
				Loading...
			</div>

			<div class="message-container">
				<MessageList :page="1" :key="details.id" :thread_id="details.id" :total="details.total"></MessageList>
			</div>
		</div>
	</div>
</template>

<script>
import { generateUrl } from '@nextcloud/router';
import axios from '@nextcloud/axios';

import MessageList from './MessageList';

export default {
	props: ['id'],
	components: {
		MessageList
	},
	data() {
		return {
			details: null,
			loading: true,
		};
	},
	computed: {
	},

	async mounted() {
		// this.threadStatus = {
		// 	topPosition: 0,
		// 	bottomPosition: 1,
		// 	count: 100,
		// 	firstLoad: true,
		// 	loading: true,
		// 	id: this.id,
		// };

		const response = await axios.get(generateUrl(`/apps/messagevault/thread/${this.id}`));
		this.details = response.data;
		this.loading = false;
	},

	methods: {

	},
};
</script>
<style scoped>
.message-container {
	position: absolute;
	top: 65px;
	bottom: 10px;
	left: 25px;
	right: 25px;
}
</style>
