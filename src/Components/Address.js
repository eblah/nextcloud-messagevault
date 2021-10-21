import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';

const Address = {};
Address._addresses = null;
Address.install = async function(Vue, options) {
	Vue.prototype.loadAddresses = async function() {
		if (Address._addresses !== null) return;

		const response = await axios.get(generateUrl('/apps/messagevault/address'));
		Address._addresses = response.data;
	};

	Vue.prototype.getAddressById = function(addressId) {
		return Address._addresses.find(x => x.id === addressId);
	};

	Vue.prototype.getThreadAddresses = function(addressIds) {
		return addressIds.map(id => {
			const add = this.getAddressById(id);
			return add.name ?? add.address;
		});
	};
};

export default Address;
