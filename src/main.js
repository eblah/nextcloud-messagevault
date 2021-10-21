import { generateFilePath } from '@nextcloud/router';

import Vue from 'vue';
import App from './App';
import Address from './Components/Address';

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, '', 'js/');

Vue.mixin({ methods: { t, n } });
Vue.use(Address);

export default new Vue({
	el: '#content',
	name: 'MessageVault',
	render: h => h(App),
});
