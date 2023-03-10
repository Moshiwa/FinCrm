<template>
    <div v-if="comments.length > 0" v-for="comment in comments">
        <el-card style="margin: 10px">
            <div class="row-right">
                <div class="row-right__upper">
                    <span v-html="comment.title" /> {{ comment.date_create }}
                </div>
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
                        <a :href="'/admin/task/' + comment.commentable_id + '/detail'">Задача</a>
                    </div>
                    <div v-else>
                        <a :href="'/admin/deal/' + comment.commentable_id + '/detail'">Сделка</a>
                    </div>
                </div>
            </div>
        </el-card>
    </div>
    <div v-else style="margin: 10px">
        Нет действий
    </div>
</template>

<script>
import Comments from "../../Components/Comments.vue";
export default {
    name: 'DetailManagerActions',
    components: {
        Comments
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
        auth: {
            type: Object,
            required: true,
        }
    },
    mounted() {
        console.log(this.user);
        $(document).on('scroll', this.loadMore);
    },
    data() {
        return {
            comments: this.user.comments,
            filter: {},
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
                axios.get(
                    '/admin/user/' + this.user.id + '/load_comments?offset=' + this.comments.length
                ).then((response) => {
                    this.comments = this.comments.concat(response.data.comments ?? []);
                    this.loading = false;
                })
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
