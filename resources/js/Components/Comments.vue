<template>
    <el-timeline-item
        v-for="comment in comments"
        class="deal-comment-item"
        :icon="definitionCommentIcon(comment)"
        :timestamp="comment.date_create"
        size="large"
        :hollow="true"
        :color="definitionCommentColor(comment)"
        placement="top"
    >
        <el-card>
        <div class="row-right">
            <div class="row-right__upper">
                <span v-html="comment.title"></span>
            </div>
            <div class="row-right__lower">
                <div class="row-right__content">
                    <div
                        v-if="comment.type === 'document'"
                        class="flex-inline"
                    >
                        <div
                            v-for="file in comment.files"
                            class="row-right__item-files"
                        >
                            <el-image
                                v-if="definitionFileType(file.meme) === 'image'"
                                style="width: 100px; height: 100px"
                                :src="file.full_path"
                                :zoom-rate="1.2"
                                :preview-src-list="[file.full_path]"
                                :initial-index="4"
                                fit="cover"
                            />
                            <div v-else>
                                <a :href="file.full_path" target="_blank">
                                    <i class="las la-file-alt"></i>
                                    {{ file.original_name }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="content-container" v-if="comment.content?.length > 0">
                            <div class="comment-content">
                                {{comment.content}}
                            </div>
                        </div>
                    </div>
                    <div>
                        <i
                            v-if="canDeleteComment(comment)"
                            class="las la-trash-alt"
                            @click="removeComment(comment)"
                        />
                    </div>
                </div>
                <div class="row-right__author">
                    <a :href="'/admin/user/' + comment.author?.id"> {{ comment.author?.name }} </a>
                </div>
            </div>
        </div>
    </el-card>
    </el-timeline-item>
</template>

<script>
import {ElMessageBox} from "element-plus";
import {Bell, ChatDotSquare, Paperclip} from "@element-plus/icons-vue";

export default {
    name: 'Comments',
    emits: ['commentSend'],
    props: {
        comments: {
            type: Array,
            required: true,
        },
        auth: {
            type: Object,
        }
    },
    mounted() {},
    data() {
        return {
            deleteCommentId: null
        }
    },
    methods: {
        canDeleteComment(comment) {
            return comment.author?.id === this.auth.id
                && (comment.content?.length > 0 || comment.files.length > 0);
        },
        removeComment(comment) {
            ElMessageBox.confirm(
                'Вы уверены?',
                'Удалить сообщение',
                {
                    confirmButtonText: 'Хорошо',
                    cancelButtonText: 'Отмена',
                    type: 'warning',
                }
            )
                .then(() => {
                    this.comments.forEach((item, index) => {
                        if (item.id === comment.id) {
                            if (comment.type === 'action') {
                                this.comments[index].content = '';
                                this.deleteCommentId = item.id;
                            } else {
                                this.comments.splice(index, 1);
                                this.deleteCommentId = item.id;
                            }
                        }
                    });

                    this.send()
                });
        },
        definitionCommentColor(comment) {
            switch (comment.type) {
                case 'document':
                    return 'grey';
                case 'action':
                    return 'pink';
                default:
                    return 'green';
            }
        },
        definitionCommentIcon(comment) {
            switch (comment.type) {
                case 'document':
                    return Paperclip;
                case 'action':
                    return Bell;
                default:
                    return ChatDotSquare;
            }
        },
        definitionFileType(meme) {
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
        send() {
            this.$emit('commentSend', this.deleteCommentId);
        }
    }
}
</script>

<style scoped>
.row-right__upper {
    display: inline-flex;
    justify-content: space-between;
    width: 100%;
}
.row-right__content {
    padding-left: 10px;
}
.row-right__content .la-trash-alt {
    cursor: pointer;
    font-size: 20px;
    color: red;
}
.row-right__content i:hover {
    filter: brightness(80%);
}
.row-right__upper > span {
    font-size: 15px;
    font-weight: 600;
    opacity: 0.5;
}
.row-right__content {
    display: flex;
    justify-content: space-between;
}
.row-right__author {
    display: flex;
    flex-direction: row;
    justify-content: end;
    gap: 10px;
    padding: 5px;
}
.flex-inline {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 10px;
}
</style>
