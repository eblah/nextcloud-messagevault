<template>
	<div class="message-list">
		<div :class="{ 'icon-loading': loading }">
			<div v-if="messages.length">
				<div v-for="message in messages">
					<MessageItem :key="message.id"
											 :body="message.body"
											 :attachments="message.attachments"
											 :timestamp="message.timestamp"
											 :address-id="message.addressId"
											 :received="message.received" />
				</div>
			</div>
			<div v-else-if="loading">
				Loading...
			</div>
			<div v-else>
				No messages were found for this thread.
			</div>
		</div>
	</div>
</template>

<script>
import axios from '@nextcloud/axios';
import {generateUrl} from '@nextcloud/router';

import MessageItem from './MessageItem';

export default {
	name: 'MessageList',
	components: {
		MessageItem
	},
	props: {
		thread_id: Number,
		page: {
			default: 1,
			type: Number
		},
		pageMaxPer: {
			default: 100,
			type: Number
		},
		total: Number
	},
	data() {
		return {
			status: {},
			pagination: {
				top: 0,
				bottom: 0,
			},
			messages: [],
			loading: true,
			firstLoad: true,
			lastMessage: null
		};
	},

	beforeMount() {
		const page = this.page - 1;
		this.pagination.top = page;
		this.pagination.bottom = page || 1; // This will subtract on first load
	},

	async mounted() {
		await this.loadMessages();
	},

	updated() {
		this.$nextTick(() => {
			if(this.firstLoad) {
				if(this.pagination.bottom === 0) {
					this.$el.scrollTo(0, this.$el.scrollHeight);
				}
				this.firstLoad = false;

				this.watchScroll();
			}

			if(this._currentScrollPosition) {
				this.$el.scrollTop = this.$el.scrollHeight - this._currentScrollPosition + this.$el.scrollTop;
			}
			this._currentScrollPosition = null;
		});
	},

	methods: {
		async loadMessages(direction = 'first') {
			if(this.loading && direction !== 'first') return false;

			const load_pos = this.getLoadPosition(direction);
			if(this.total <= load_pos * this.pageMaxPer) return false;

			this.loading = true;

			const response = await axios.get(generateUrl(`/apps/messagevault/thread/${this.thread_id}/messages?position=${load_pos}&limit=${this.pageMaxPer}`));

			if(direction === 'top') {
				// Save current scroll position
				this._currentScrollPosition = this.$el.scrollHeight;
				response.data.forEach((msg) => {
					this.messages.unshift(msg);
				});
			} else {
				response.data.reverse();
				this.messages = response.data;
			}

			// @todo cleanup messages outside of the viewport?

			this.loading = false;
		},

		watchScroll() {
			this.$el.onscroll = (() => {
				//let top = this.$refs.messageList.scrollTop;// this.$refs.messageList.offsetHeight;
				if(this.$el.scrollTop < 150 && this.$el.scrollTop > 0) this.loadMessages('top');
				// @todo also find the bottom
				//if(this.$refs.messageList.scrollTop < 50 && this.$refs.messageList.scrollTop > 0) console.log('load moar');
			});
		},

		getLoadPosition(position) {
			if(position === 'top') {
				this.pagination.top += 1;
				return this.pagination.top;
			} else {
				this.pagination.bottom -= 1;
				return this.pagination.bottom;
			}
		},
	}
};
</script>

<style scoped>
.message-list {
	overflow: scroll;
	height: 100%;
	max-width: 1000px;
	margin: auto;
}
</style>
