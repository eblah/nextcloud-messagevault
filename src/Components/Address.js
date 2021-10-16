import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';

let Address = {};
Address._addresses = null;
Address.install = async function(Vue, options) {
	Vue.prototype.loadAddresses = async function() {
		if(Address._addresses !== null) return;

		const response = await axios.get(generateUrl('/apps/messagevault/address'));
		Address._addresses = response.data;
	}

	Vue.prototype.getAddressById = function(address_id) {
		return Address._addresses.find(x => x.id === address_id);
	}
}

export default Address;
