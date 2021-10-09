<template>
	<div id="content" class="app-smsbackupvault">
		<AppNavigation>
			<template #list>
					<AppNavigationItem v-for="thread in threadList"
						:key="thread.id"
						:title="thread.name"
						:class="{active: currentThreadId === thread.id}"
						@click="openThread(thread)">
						<template slot="actions">
	<!--						<ActionButton-->
	<!--							icon="icon-delete"-->
	<!--							@click="deleteThread(thread)">-->
	<!--							{{ t('smsbackupvault', 'Delete Thread') }}-->
	<!--						</ActionButton>-->
						</template>
					</AppNavigationItem>
			</template>
			<template #footer>
				<AppNavigationSettings :title="t('smsbackupvault', 'Settings')">
				</AppNavigationSettings>
			</template>
		</AppNavigation>
		<AppContent>
			<div class="section" v-if="currentThread">
				<h1>{{ currentThread.details.name }}</h1>

				<div v-if="threadStatus.loading">
					Loading...
				</div>
				<div id="smsbackupvaultMessageList" ref="messageList" v-else-if="currentThread.messages.length && !threadStatus.loading">
					<div v-if="threadStatus.loading">Loading...</div>
					<div v-else-if="!threadStatus.loading">
						<div v-for="message in currentThread.messages" style="padding: 10px; background: #ddd; margin-bottom: 5px">
							<div v-if="message.received == 1">
								Received:
							</div>
							<div v-else>
								Sent:
							</div>
							<div v-for="file in message.attachments">
								<img v-if="file.filetype.match('image')" :src="file.url" :height="file.height" :width="file.width" style="max-width: 400px; height: auto;">
								<video v-else-if="file.filetype.match('video')" width="400" controls>
									<source :src="file.url" :type="file.filetype">
								</video>
							</div>
							{{ message.body }}
						</div>
					</div>
				</div>
				<div v-else>
					Could not find any messages.
				</div>
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('smsbackupvault', 'Select a message thread to view...') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton';
import AppContent from '@nextcloud/vue/dist/Components/AppContent';
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation';
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem';
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew';

import '@nextcloud/dialogs/styles/toast.scss';
import { generateUrl } from '@nextcloud/router';
import { showError, showSuccess } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';

export default {
	name: 'SmsBackupVault',
	appPath: '/apps/smsbackupvault',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
	},
	data() {
		return {
			threadStatus: {
				loading: false
			},
			threadList: [],
			currentThreadId: null,
			currentThread: null,
			updating: false,
			loading: true,
		};
	},
	computed: {
	},
	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/smsbackupvault/thread'));
			this.threadList = response.data;
		} catch (e) {
			console.error(e);
			showError(t(this.name, 'Could not load threads.'));
		}
		this.loading = false;
	},

	updated() {
		this.$nextTick(() => {
			if(!this.threadStatus.loading && this.threadStatus.firstLoad) {
				if(this.threadStatus.topPosition === 0) {
					this.$refs.messageList.scrollTo(0, this.$refs.messageList.scrollHeight);
				}
				this.threadStatus.firstLoad = false;

				this.watchScroll();
			}

			if(this._currentScrollPosition) {
				this.$refs.messageList.scrollTop = this.$refs.messageList.scrollHeight - this._currentScrollPosition + this.$refs.messageList.scrollTop;
			}
			this._currentScrollPosition = null;
		});
	},

	methods: {
		watchScroll() {
			if(!this.$refs.messageList) return;

			this.$refs.messageList.onscroll = (() => {
				//let top = this.$refs.messageList.scrollTop;// this.$refs.messageList.offsetHeight;
				if(this.$refs.messageList.scrollTop < 150 && this.$refs.messageList.scrollTop > 0) this.loadMessages('top');
				// @todo also find the bottom
				//if(this.$refs.messageList.scrollTop < 50 && this.$refs.messageList.scrollTop > 0) console.log('load moar');
			});
		},

		getLoadPosition(position) {
			if(position === 'top') {
				this.threadStatus.topPosition += 1;
				return this.threadStatus.topPosition;
			}
			else {
				this.threadStatus.bottomPosition -= 1;
				return this.threadStatus.bottomPosition;
			}
		},

		async loadMessages(direction = 'first') {
			if(this.loading) return false;
			this.loading = true;

			const load_pos = this.getLoadPosition(direction);
			if(this.currentThread.details.total <= load_pos * this.threadStatus.count) {
				this.loading = false;
				return false;
			}
			const response = await axios.get(generateUrl(`/apps/smsbackupvault/thread/${this.threadStatus.id}/messages?position=${load_pos}&limit=${this.threadStatus.count}`));
			response.data.reverse();
			if(direction === 'top') {
				// Save current scroll position
				this._currentScrollPosition = this.$refs.messageList.scrollHeight; // this.$refs.messageList.scrollTop;
				response.data.forEach((msg) => {
					this.currentThread.messages.unshift(msg);
				});
			} else this.currentThread.messages = response.data;

			this.threadStatus.loading = false;

			this.loading = false;
		},

		async openThread(thread) {
			if(this.loading) return false;
			this.loading = true;

			this.threadStatus = {
				topPosition: 0,
				bottomPosition: 1,
				count: 100,
				firstLoad: true,
				loading: true,
				id: thread.id,
			};

			const response = await axios.get(generateUrl(`/apps/smsbackupvault/thread/${thread.id}`));
			this.currentThread = {
				details: response.data,
				messages: [],
			};
			this.loading = false;

			await this.loadMessages();
		},

		async deleteThread(thread) {

		},
	},
};
</script>
<style scoped>
  .section {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
	}
	#smsbackupvaultMessageList {
		overflow: scroll;
		height: 100%;
	}
</style>
