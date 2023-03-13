<template>
    <div class="card-body flex-column">
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
                class="btn btn-sm btn-link text-capitalize"
                @click="deleteAction"
            >
                <i class="la la-trash" />
                Удалить
            </a>
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
</template>

<script>

import ActionButton from "./ActionButton.vue";
import {ElNotification} from "element-plus";
export default {
    name: 'ActionButtons',
    emits: ['changeData', 'commentSend'],
    components: {
        ActionButton
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
        change(button) {
            this.$emit('changeData', button);
        },
        deleteAction() {
            this.$emit('deleteAction');
        }
    }
}
</script>

<style scoped>
.flex-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    width: 200px;
}
</style>
