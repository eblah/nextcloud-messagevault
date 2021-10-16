<template>
	<div class="section">
		<div v-if="details">
			<h2>{{ details.name }}</h2>
			<div class="stats">
				{{ n('messagevault', '{total} message', '{total} messages', details.total, {total: details.total}) }}
			</div>

			<div class="message-container" :class="{ 'icon-loading': loading }">
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
.section {
	padding: 7px 30px 30px 45px
}

.stats {
	margin-top: -15px;
	font-size: 11px;
}
</style>
