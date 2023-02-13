<template>
    <div class="card-body flex-column">
        <div v-for="setting in settings">
            <el-button
                v-if="setting.pivot.is_enable"
                class="w-100"
                @click="buttonClick(setting)"
            >
                {{ setting.description }}
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
        settings: {

        }
    },
    computed: {
        settings() {
            return this.settings;
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
        buttonClick(setting) {
            if (setting.name === 'comment' && setting.type === 'button') {
                this.commentClick();
            }

            if (setting.name === 'file-upload' && setting.type === 'button') {
                this.fileUploadClick();
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
