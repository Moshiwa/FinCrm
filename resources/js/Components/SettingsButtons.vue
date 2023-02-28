<template>
    <div class="card-body flex-column">
        <div v-for="button in stageButtons">
            <custom-button
                v-if="button.visible.some(item => item.id === stage.id)"
                :button="button"
                @click="buttonClick(button)"
            />
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

import CustomButton from "./CustomButton.vue";
export default {
    name: 'SettingsButton',
    components: {
        CustomButton
    },
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

            this.$emit('commentSend', data);
            this.comment = '';
            this.visibleCommentForm = false;
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
