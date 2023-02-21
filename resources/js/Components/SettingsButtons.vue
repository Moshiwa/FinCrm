<template>
    <div class="card-body flex-column">
        <div v-for="button in stageButtons">
            <el-button
                v-if="button.options.display.stages.some(item => item === stage.id)"
                class="w-100"
                @click="buttonClick(button)"
            >
                {{ button.name }}
            </el-button>
        </div>
    </div>


    <el-drawer v-model="visibleCommentForm" :show-close="false">
        <template #header="{ close, titleId, titleClass }">
            <h4 :id="titleId" :class="titleClass">Оставте комментарий</h4>
        </template>
        <el-input
            v-model="comment"
            rows="5"
            type="textarea"
        />
        <el-button
            class="w-100 mt-3"
            type="success"
            @click="commentSend"
        >
            Отправить
        </el-button>
    </el-drawer>
    <el-drawer v-model="visibleFileUploadForm" :show-close="false">
        <template #header="{ close, titleId, titleClass }">
            <h4 :id="titleId" :class="titleClass">Выберите файлы</h4>
        </template>
        <file-upload @send="fileUploadSend($event)"/>
    </el-drawer>
</template>

<script>

import FileUpload from "./FileUpload.vue";

export default {
    name: 'SettingsButton',
    components: { FileUpload },
    props: {
        buttons: {
            type: Array,
            default: []
        },
        stage: {
            type: Object,
            default: {}
        }
    },
    computed: {
        stageButtons() {
            return this.buttons ?? [];
        },
        stage() {
            return this.stage ?? {};
        }
    },
    data() {
        return {
            visibleCommentForm: false,
            visibleFileUploadForm: false,
            comment: '',
        }
    },
    methods: {
        buttonClick(button) {
            if (button.key === 'leave_comment') {
                this.commentClick();
            } else if (button.key === 'upload_document') {
                this.fileUploadClick();
            } else {
                this.change(button.options)
            }
        },

        commentClick() {
            this.visibleCommentForm = true
        },
        commentSend() {
            this.$emit('commentSend', this.comment);
            this.visibleCommentForm = false;
        },

        fileUploadClick() {
            this.visibleFileUploadForm = true
        },
        fileUploadSend(e) {
            this.$emit('fileUploadSend', e);
            this.visibleFileUploadForm = false;
        },

        change(button) {
            this.$emit('changeData', button);
        }
    }
}
</script>

<style scoped>
.flex-column {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 200px;
}
</style>
