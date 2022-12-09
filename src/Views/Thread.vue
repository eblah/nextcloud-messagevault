<template>
	<div class="section">
		<div v-if="details">
      <div class="info">
        <h2>{{ title }}</h2>
        <div class="stats">
          {{ n('messagevault', '{total} message', '{total} messages', total, {total: details.total}) }}
        </div>
      </div>
      <div class="search" v-if="searchEnabled">
        <input v-model="threadSearch" @input="threadSearch" type="text" :placeholder="t('messagevault', 'Search this thread…')">
      </div>
    </div>

    <div class="message-container" :class="{ 'icon-loading': loading }">
      <MessageList :key="details.id+threadSearch"
                   :page="startPage"
                   :thread-id="details.id"
                   :search="threadSearch"
                   :total="details.total" />
    </div>
	</div>
</template>

<script>
import { generateUrl } from '@nextcloud/router';
import axios from '@nextcloud/axios';

import MessageList from './MessageList';

export default {
	components: {
		MessageList,
	},
	props: {
		id: Number,
		startPage: {
			default: 1,
			type: Number,
		},
		searchEnabled: false,
	},
	data() {
		return {
			details: null,
			loading: true,
			total: 0,
			threadSearch: this.$route.params.searchTerm || '',
		};
	},
	computed: {
		title() {
			return this.getThreadAddresses(this.details.addressIds)
				.join(', ');
		},
	},

	async mounted() {
		await this.getMetadata()
	},

	methods: {
		async getMetadata() {
			this.loading = true;
			const response = await axios.get(generateUrl(`/apps/messagevault/thread/${this.id}?search=${this.threadSearch}`));
			this.details = response.data;
			this.total = this.details.total
			this.loading = false;
		}
	},

	watch: {
		$route(to, from) {
			if(to.name === 'thread-search') {
				this.getMetadata()
			}
		},

		async threadSearch(new_search) {
			this.$router.push({ path: `/t/${this.id}/s/${new_search}` })
			await this.getMetadata()
		}
	}
};
</script>
<style scoped>
.message-container {
	position: absolute;
	top: 65px;
	bottom: 10px;
}

.section {
	padding: 7px 30px 30px 45px
}

.stats {
	margin-top: -15px;
	font-size: 11px;
}

.info {
	float: left
}

.search {
	float: right
}
</style>
