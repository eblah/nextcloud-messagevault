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
