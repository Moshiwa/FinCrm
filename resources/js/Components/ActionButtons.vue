<template>
    <div class="card-body flex-column common-gap">
        <div v-for="button in stageButtons">
            <action-button
                v-if="button.visible.some(item => item.id === thisStage.id)"
                :button="button"
                @click="buttonClick(button)"
            />
        </div>

        <el-divider />

        <div v-if="isDelete">
            <a
                class="btn btn-sm btn-link text-capitalize sys-button"
                @click="deleteEntityAction"
            >
                <i class="la la-trash" />
                Удалить
            </a>
        </div>

        <div v-if="isUpload">
            <a
                class="btn btn-sm btn-link text-capitalize sys-button"
                @click="visibleFileUploadForm = true"
            >
                <i class="la la-file-upload" />
                Загрузить
            </a>
        </div>
    </div>

<!--Окно загрузки файлов-->
    <el-drawer v-model="visibleFileUploadForm" :show-close="false">
        <template #header="{ close, titleId, titleClass }">
            <h4 :id="titleId" :class="titleClass">Выберите файлы</h4>
        </template>
        <file-upload @send="filesSend($event)"/>
    </el-drawer>
<!--Окно для текста комменатрия-->
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

</template>

<script>

import ActionButton from "./ActionButton.vue";
import {ElMessageBox, ElNotification} from "element-plus";
import FileUpload from "../Components/FileUpload.vue";

export default {
    name: 'ActionButtons',
    emits: ['changeData', 'commentSend', 'filesSend', 'deleteAction'],
    components: {
        ActionButton, FileUpload
    },
    props: {
        buttons: {
            type: Array,
            default: []
        },
        stage: {
            type: Object,
            default: {}
        },
        isDelete: {
            type: Boolean,
            default: false
        },
        isUpload: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        stageButtons() {
            return this.buttons ?? [];
        },
        thisStage() {
            return this.stage ?? {};
        }
    },
    data() {
        return {
            visibleCommentForm: false,
            visibleFileUploadForm: false,
            errorMessage: false,
            comment: '',

            currentButton: {}
        }
    },
    methods: {
        buttonClick(button) {
            this.currentButton = button;
            if (button.action.comment === true) {
                this.commentClick();
            } else {
                this.change(button.action)
            }
        },

        commentClick() {
            this.visibleCommentForm = true
        },
        commentSend() {
            let data = {
                comment: this.comment,
                button: this.currentButton,
            }

            if (this.comment) {
                this.$emit('commentSend', data);
                this.comment = '';
                this.visibleCommentForm = false;
            } else {
                ElNotification({
                    title: 'Напишите комментарий',
                    type: 'error',
                    position: 'bottom-right',
                });
            }
        },
        filesSend(e) {
            this.$emit('filesSend', e);
            this.visibleFileUploadForm = false;
        },
        change(button) {
            this.$emit('changeData', button);
        },
        deleteEntityAction() {
            ElMessageBox.confirm(
                'Вы уверены?',
                'Удалить',
                {
                    confirmButtonText: 'Хорошо',
                    cancelButtonText: 'Отмена',
                    type: 'warning',
                }
            )
                .then(() => {
                    this.$emit('deleteAction');
                })
        }
    }
}
</script>

<style scoped>

</style>
