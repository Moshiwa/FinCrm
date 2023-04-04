<template>
    <div style="overflow: hidden;">
        <filters :filter="filter" :daterange-filter="true"/>

        <div v-if="comments.length > 0" v-for="comment in comments">
            <el-card style="margin: 10px">
                <div class="row-right">
                    <div class="row-right__upper">
                        <span v-html="comment.title" /> {{ comment.date_create }}
                    </div>
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
                    </div>
                    <div class="row-right__author">
                        <div v-if="comment.commentable_type === 'App\\Models\\Task'">
                            <a :href="'/admin/task/' + comment.commentable_id + '/show'">Задача</a>
                        </div>
                        <div v-else>
                            <a :href="'/admin/deal/' + comment.commentable_id + '/show'">Сделка</a>
                        </div>
                    </div>
                </div>
            </el-card>
        </div>
        <div v-else style="margin: 10px">
            <el-empty description="Менеджер не совершал каких-либо действий" />
        </div>
        <div v-loading="loading"></div>
    </div>
</template>

<script>
import Filters  from "../../Components/Filters.vue";
import CommentDocument from '../../Components/CommentsTypes/Document.vue'
import CommentAudio from '../../Components/CommentsTypes/Audio.vue'
export default {
    name: 'DetailManagerActions',
    components: {
        Filters, CommentDocument, CommentAudio
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
        auth: {
            type: Object,
            required: true,
        },
        filter: {
            type: Array,
            required: false,
        }
    },
    mounted() {
        $(document).on('scroll', this.loadMore);
    },
    data() {
        return {
            comments: this.user.comments,
            loading: false,
        }
    },
    methods: {
        loadMore (e) {
            if (this.loading) {
                return;
            }
            let can = false;
            let currentPos = window.pageYOffset;
            let pos = document.body.offsetHeight - window.innerHeight;
            if (pos <= (currentPos + 3)) {
                can = true;
            }

            if (can) {
                this.loading = true;

                let url = '/admin/user/' + this.user.id + '/load_comments?';
                url += window.location.search
                if (this.comments?.length > 0) {
                    url+= '&offset=' +  this.comments.length;
                }

                axios.get(url).then((response) => {
                    this.comments = this.comments.concat(response.data.comments ?? []);
                    this.loading = false;
                })
            }
        },
        isImage(meme) {
            switch (meme) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'svg':
                    return true
                default:
                    return false
            }
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
