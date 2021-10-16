<template>
	<div class="attachment">
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
	</div>
</template>

<script>
export default {
	name: 'Attachment',
	props: {
		filetype: String,
		url: String,
		height: [Number, null],
		width: [Number, null]
	},
	data() {
		return {

		};
	},
	computed: {
	}
};
</script>

<style scoped>
.attachment img {
	max-width: 400px;
	max-height: 400px;
	width: auto;
}

.attachment img,
.attachment video {
	border-radius: 10px;
}
</style>
