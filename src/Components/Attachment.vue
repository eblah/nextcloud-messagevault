<!--
 - @copyright Copyright (c) 2022 Justin Osborne <justin@eblah.com>
 -
 - @author Justin Osborne <justin@eblah.com>
 -
 - @license AGPL-3.0-or-later
 -
 - This program is free software: you can redistribute it and/or modify
 - it under the terms of the GNU Affero General Public License as
 - published by the Free Software Foundation, either version 3 of the
 - License, or (at your option) any later version.
 -
 - This program is distributed in the hope that it will be useful,
 - but WITHOUT ANY WARRANTY; without even the implied warranty of
 - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 - GNU Affero General Public License for more details.
 -
 - You should have received a copy of the GNU Affero General Public License
 - along with this program. If not, see <http://www.gnu.org/licenses/>.
 -
 -->
 <template>
	<div class="attachment">
		<a :href="url"
			:download="name">
			<img v-if="filetype.match('image')"
				 :src="url"
				 :height="height"
				 :width="width">
			<video v-else-if="filetype.match('video')"
						width="400"
						controls>
				<source :src="url"
								:type="filetype">
			</video>
			<span v-else>
				{name}
			</span>
		</a>
	</div>
</template>

<script>
export default {
	name: 'Attachment',
	props: {
		filetype: String,
		url: String,
		height: [Number, null],
		width: [Number, null],
		name: String,
	},
	data() {
		return {

		};
	},
	computed: {
	},
};
</script>

<style scoped>
.attachment img {
	max-width: 400px;
	max-height: 400px;
	height: auto;
	object-fit: contain;
}

.attachment img,
.attachment video {
	border-radius: 10px;
}
</style>
