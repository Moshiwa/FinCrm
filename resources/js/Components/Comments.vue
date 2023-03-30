<template>
    <el-timeline-item
        v-for="(comment, index) in thisComments"
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
                    <div
                        v-if="comment.type === 'document'"
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
                                    {{ file.original_name }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="audio-container" v-else-if="comment.type === 'audio'">
                        <audio
                            :src="comment.content"
                            :id="'audio_player_' + index"
                            controls
                            @play="stopAllExceptCurrent('audio_player_' + index)"
                        />
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
                <div class="row-right__author common-gap row">
                    <a :href="'/admin/user/' + comment.author?.id"> {{ comment.author?.name }} </a>
                </div>
            </div>
        </div>
        <el-divider/>
    </el-timeline-item>
</template>

<script>
import {ElMessageBox} from "element-plus";
import {Bell, ChatDotSquare, Paperclip, Position} from "@element-plus/icons-vue";

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
    data() {
        return {
            thisComments: this.comments ?? [],
            deleteCommentId: null
        }
    },
    methods: {
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
        canDeleteComment(comment) {
            return comment.author?.id === this.auth.id
                && (comment.content?.length > 0 || comment.files?.length > 0)
                && comment.type !== 'remote'
                && comment.type !== 'audio';
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
                    this.thisComments.forEach((item, index) => {
                        if (item.id === comment.id) {
                            if (comment.type === 'action') {
                                this.thisComments[index].content = '';
                                this.deleteCommentId = item.id;
                            } else {
                                this.thisComments.splice(index, 1);
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
                case 'remote':
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
