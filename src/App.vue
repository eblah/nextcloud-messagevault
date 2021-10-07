<template>
	<div id="content" class="app-smsbackupvault">
		<AppNavigation>
			<ul>
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
			</ul>
		</AppNavigation>
		<AppContent>
			<div class="section" v-if="currentThread">
          <h1>{{ currentThread.details.name }}</h1>
          <div id="smsbackupvaultMessageList" ref="messageList">
            <div v-for="message in currentThread.messages" style="padding: 10px; background: #ddd; margin-bottom: 5px">
              <div v-if="message.received == 1">
                Received:
              </div>
              <div v-else>
                Sent:
              </div>
              {{ message.body }}
            </div>
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

	methods: {
		watchScroll() {
			if(!this.$refs.messageList) return;

			return;
			this.$refs.messageList.scrollTo(0, this.$refs.messageList.scrollHeight);

			this.$refs.messageList.onscroll = (() => {
				let top = this.$refs.messageList.scrollTop;// this.$refs.messageList.offsetHeight;
				if(this.$refs.messageList.scrollTop < 50 && this.$refs.messageList.scrollTop > 0) alert('load moar');
				//this.$refs.messageList.scrollTo(0, this.$refs.messageList.scrollHeight);
			});
		},

		async openThread(thread) {
			this.loading = true;

			const response = await axios.get(generateUrl(`/apps/smsbackupvault/thread/${thread.id}`));
			this.currentThread = response.data;

			this.loading = false;

			setTimeout(() => this.watchScroll(), 5000);
		},

		async deleteThread(thread) {

		},
	},
};
</script>
<style scoped>
  #smsbackupvaultMessageList {
    overflow: scroll;
    height: 75vh;
  }
</style>
