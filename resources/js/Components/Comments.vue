<template>
    <el-timeline-item
        v-for="(comment, index) in comments"
        class="deal-comment-item"
        :icon="definitionCommentIcon(comment)"
        :timestamp="comment.date_create"
        size="large"
        :hollow="true"
        :color="definitionCommentColor(comment)"
        placement="top"
    >

        <div class="row-right">
            <div class="row-right__upper">
                <span v-html="comment.title"></span>
            </div>
            <div class="row-right__lower">
                <div class="row-right__content">
                    <comment-document
                        v-if="comment.type === 'document'"
                        :comment="comment"
                    />
                    <comment-audio
                        v-else-if="comment.type === 'audio' && !!comment.content"
                        :comment="comment"
                        @stopAll="stopAllExceptCurrent($event)"
                    />
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
                <div class="row-right__author common-gap row">
                    {{ comment.author?.name }}
                </div>
            </div>
        </div>
        <el-divider/>
    </el-timeline-item>
</template>

<script>
import {ElMessageBox} from "element-plus";
import {Bell, ChatDotSquare, Paperclip, Position, Headset} from "@element-plus/icons-vue";
import CommentDocument from './CommentsTypes/Document.vue'
import CommentAudio from './CommentsTypes/Audio.vue'

export default {
    name: 'Comments',
    emits: ['commentSend'],
    components: {
        CommentDocument,
        CommentAudio,
    },
    props: {
        comments: {
            type: Array,
            required: true,
        },
        auth: {
            type: Object,
        }
    },
    data() {
        return {
            deleteCommentId: null,
            permissions: {
                can_delete: this.auth.permission_names.find((item) => item === 'comments.delete'),
                can_delete_self: this.auth.permission_names.find((item) => item === 'comments.delete_self'),
            },
        }
    },
    mounted() {
        console.log(this.comments)
    },
    methods: {
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
        stopAllExceptCurrent(ref) {
            let index = 0;
            this.comments.forEach((comment) => {
                if (comment.type === 'audio') {
                    let id = 'audio_player_' + index;
                    if (id !== ref) {
                        id = '#' + id;
                        let audio = document.querySelector(id);
                        audio.pause();
                        audio.currentTime = 0;
                    }
                    index++;
                }
            })
        },
        definitionCommentColor(comment) {
            switch (comment.type) {
                case 'document':
                    return 'grey';
                case 'action':
                    return 'pink';
                case 'remote':
                case 'audio':
                    return '#3c81d5';
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
                case 'remote':
                    return Position;
                case 'audio':
                    return Headset;
                default:
                    return ChatDotSquare;
            }
        },
        canDeleteComment(comment) {
            /*return comment.author?.id === this.auth.id
                && (comment.content?.length > 0 || comment.files?.length > 0)
                && comment.type !== 'remote'
                && comment.type !== 'audio';*/

            if (this.permissions.can_delete) {
                return true;
            }

            if (this.permissions.can_delete_self) {
                if (comment.author?.id === this.auth.id) {
                    return true;
                }
            }

            return false;
        },
        send() {
            this.$emit('commentSend', this.deleteCommentId);
        }
    }
}
</script>
