<template>
    <div class="audio-container">
        <audio
            v-if="isUrl(comment.content)"
            :src="comment.content"
            :id="'audio_player_' + index"
            controls
            @play="stopAllExceptCurrent('audio_player_' + index)"
        />
        <div v-else class="comment-content">
            {{comment.content}}
        </div>
    </div>
</template>

<script>
export default {
    name: 'Audio',
    props: {
        comment: {
            type: Object,
            required: true
        }
    },
    methods: {
        stopAllExceptCurrent(ref) {
            this.$emit('send', ref);
        },
        isUrl(str) {
            try {
                new URL(str);
                return true;
            } catch {
                return false;
            }
        },
    }
}
</script>

<style scoped>

</style>
