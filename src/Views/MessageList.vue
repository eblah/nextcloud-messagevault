<template>
	<div class="message-list">
		<div :class="{ 'icon-loading': loading }">
			<div v-if="messages.length">
				<MessageItem  v-for="message in messages"
											:key="message.id"
											:body="message.body"
											:attachments="message.attachments"
											:timestamp="message.timestamp"
											:address-id="message.addressId"
											:received="message.received" />
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
import { generateUrl } from '@nextcloud/router';

import MessageItem from './MessageItem';

export default {
	name: 'MessageList',
	components: {
		MessageItem,
	},
	props: {
		threadId: Number,
		page: {
			default: 1,
			type: Number,
		},
		pageMaxPer: {
			default: 100,
			type: Number,
		},
		total: Number,
		maxPagesInViewport: {
			default: 4,
			type: Number,
		},
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
			lastMessage: null,
		};
	},

	beforeMount() {
		const page = (this.page || 1) - 1;
		this.pagination.top = page;
		this.pagination.bottom = page || 1; // This will subtract on first load
	},

	async mounted() {
		await this.loadMessages();
	},

	updated() {
		this.$nextTick(() => {
			if (this.firstLoad) {
				if (this.pagination.bottom === 0) {
					this.$el.scrollTo(0, this.$el.scrollHeight);
				}
				this.firstLoad = false;

				this.watchScroll();
			}

			if (this._currentScrollPosition) {
				if (this._lastDirection === 'top') {
					this.$el.scrollTop = this.$el.scrollHeight - this._currentScrollPosition + this.$el.scrollTop;
				}

				this.cleanup(this._lastDirection);
			}
			if (this._cleanupScrollPosition) {
				this.$el.scrollTop = this.$el.scrollHeight - this._cleanupScrollPosition + this.$el.scrollTop;
				this._cleanupScrollPosition = null;
			}
			this._currentScrollPosition = null;
		});
	},

	methods: {
		async loadMessages(direction = 'first') {
			if (this.loading && direction !== 'first') return false;

			const loadPos = this.getLoadPosition(direction);
			if (loadPos === null) return false;

			this.loading = true;

			const response = await axios.get(generateUrl(`/apps/messagevault/thread/${this.threadId}/messages?position=${loadPos}&limit=${this.pageMaxPer}`));

			if (direction === 'top') {
				// Save current scroll position
				response.data.forEach((msg) => {
					this.messages.unshift(msg);
				});
			} else {
				response.data.reverse();
				response.data.forEach((msg) => {
					this.messages.push(msg);
				});
			}

			if (direction !== 'first') {
				this._currentScrollPosition = this.$el.scrollHeight;
				this._lastDirection = direction;
			}
			this.loading = false;
		},

		cleanup(dir) {
			this._cleanupScrollPosition = this.$el.scrollHeight;
			const max = this.maxPagesInViewport * this.pageMaxPer;

			if (this.messages.length <= max) return;
			const remainder = this.messages.length % this.pageMaxPer;
			const knockoff = remainder || this.pageMaxPer;

			const start = remainder > 0
				? (this.pageMaxPer * (this.maxPagesInViewport - 1)) + remainder
				: max;

			if (dir === 'top') {
				++this.pagination.bottom;
				this.messages.splice(start, this.pageMaxPer);
			} else {
				--this.pagination.top;
				this.messages.splice(0, knockoff);
			}
		},

		watchScroll() {
			this.$el.onscroll = (() => {
				const h = this.$el.scrollHeight - this.$el.offsetHeight;
				if (this.$el.scrollTop < 150 && this.$el.scrollTop > 0) this.loadMessages('top');
				else if (this.$el.scrollTop > (h - 150) && this.$el.scrollTop < h) {
					this.loadMessages('bottom');
				}
			});
		},

		getLoadPosition(position) {
			if (position === 'top') {
				if (this.total <= (this.pagination.top + 1) * this.pageMaxPer) return null;
				return ++this.pagination.top;
			} else {
				if ((this.pagination.bottom - 1) === -1) return null;
				return --this.pagination.bottom;
			}
		},
	},
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
