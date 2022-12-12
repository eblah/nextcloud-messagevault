/**
 * @copyright 2022 Justin Osborne <justin@eblah.com>
 * 
 * @author Justin Osborne <justin@eblah.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
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
