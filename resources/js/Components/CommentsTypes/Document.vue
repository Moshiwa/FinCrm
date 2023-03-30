<template>
    <div
        class="row common-gap"
    >
        <div
            v-for="file in comment.files"
            class="row-right__item-files"
        >
            <el-image
                v-if="definitionFileType(file.meme) === 'image'"
                style="width: 100px; height: 100px; border-radius: 4px;"
                :src="file.full_path"
                :zoom-rate="1.2"
                :preview-src-list="[file.full_path]"
                :initial-index="4"
                fit="cover"
            />
            <div v-else>
                <a :href="file.full_path" target="_blank">
                    <i class="las la-file-alt"></i>
                    efew
                    {{ file.original_name }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Document',
    props: {
        comment: {
            type: Object,
            required: true
        }
    },
    methods: {
        definitionFileType(meme) {
            console.log(meme)
            switch (meme) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'svg':
                    return 'image'
                default:
                    return 'document'
            }
        },
    }
}
</script>

<style scoped>

</style>
