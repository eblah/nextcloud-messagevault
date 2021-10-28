import { generateFilePath } from '@nextcloud/router';

import Vue from 'vue';
import App from './MessageVaultRoot';
import Address from './Components/Address';
import router from './Router';

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, '', 'js/');

Vue.mixin({ methods: { t, n } });
Vue.use(Address);

export default new Vue({
	el: '#content',
	name: 'MessageVault',
	router,
	render: h => h(App),
});
