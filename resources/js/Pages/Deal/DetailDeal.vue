<template>
    <div class="wrap">
        <div class="card">
            <div class="card-left">
                <el-collapse v-model="active">
                    <el-collapse-item title="Общее" name="1">

                        <el-descriptions
                            direction="vertical"
                            column="1"
                            size="large"
                            border
                        >
                            <el-descriptions-item label="Наименование">
                                <contenteditable
                                    v-model="deal.name"
                                    @send="send"
                                />
                            </el-descriptions-item>
                            <el-descriptions-item label="Воронка">
                                <div class="custom-field__container">
                                    <el-select
                                        v-model="pipeline"
                                        value-key="id"
                                        @change="changePipeline"
                                    >
                                        <el-option
                                            v-for="pipeline in allPipelines"
                                            :key="pipeline.id"
                                            :label="pipeline.name"
                                            :value="pipeline"
                                        />
                                    </el-select>
                                    <el-select
                                        v-model="stage"
                                        value-key="id"
                                        @change="changeStage"
                                    >
                                        <el-option
                                            v-for="stage in allStages"
                                            :key="stage.id"
                                            :label="stage.name"
                                            :value="stage"
                                        />
                                    </el-select>
                                </div>
                            </el-descriptions-item>
                            <el-descriptions-item label="Ответственный">
                                <el-select
                                    v-model="responsible"
                                    value-key="id"
                                    filterable
                                    remote
                                    reserve-keyword
                                    placeholder="Please enter a keyword"
                                    :remote-method="getUsers"
                                    @change="send"
                                >
                                    <el-option
                                        v-for="user in responsibles"
                                        :key="user.id"
                                        :label="user.name"
                                        :value="user"
                                    />
                                </el-select>
                            </el-descriptions-item>
                            <el-descriptions-item v-for="field in dealFields" :label="field.name">
                                <field
                                    :field="field"
                                    @send="send"
                                />
                            </el-descriptions-item>
                        </el-descriptions>
                    </el-collapse-item>
                </el-collapse>

                <el-collapse v-model="active">
                    <el-collapse-item title="Данные о клиенте" name="2">
                        <el-descriptions
                            direction="vertical"
                            column="1"
                            size="large"
                            border
                        >
                            <el-descriptions-item label="Имя">
                                <contenteditable
                                    v-model="client.name"
                                    @send="send"
                                />
                            </el-descriptions-item>
                            <el-descriptions-item v-for="field in clientFields" :label="field.name">
                                <field
                                    :field="field"
                                    @send="send"
                                />
                            </el-descriptions-item>
                        </el-descriptions>
                    </el-collapse-item>
                </el-collapse>
            </div>

            <div class="card-right">
                <div class="card-body row">
                    <div>
                        <el-select
                            class="m-3"
                        >
                            <el-option

                            />
                        </el-select>
                        <el-button @click="visibleFileUploadForm = true">Прикрепить документ</el-button>
                    </div>
                    <el-timeline @scroll="loadMore" class="infinite-list" style="overflow: auto" ref="scroll_container">
                            <el-timeline-item
                                v-for="comment in comments"
                                class="deal-comment-item"
                                :timestamp="comment.created_at"
                                :icon="definitionCommentIcon(comment)"
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
                                                <contenteditable
                                                    v-if="comment.type === 'comment'"
                                                    v-model="comment.content"
                                                    @send="send"
                                                />
                                                <div
                                                    v-else-if="comment.type === 'action'"
                                                >
                                                    <span v-html="comment.content"></span>
                                                </div>
                                                <div
                                                    v-else-if="comment.type === 'document'"
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
                                                            <a :href="file.full_path" target="_blank">Doc</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row-right__author">{{ comment.author?.name }}</div>
                                        </div>
                                    </div>
                                </el-card>
                            </el-timeline-item>
                    </el-timeline>
                </div>
            </div>
        </div>

        <settings-button
            :buttons="stageButtons"
            :stage="stage"

            @commentSend="sendComment($event)"
            @changeData="changeData($event)"
        />
    </div>

    <el-drawer v-model="visibleFileUploadForm" :show-close="false">
        <template #header="{ close, titleId, titleClass }">
            <h4 :id="titleId" :class="titleClass">Выберите файлы</h4>
        </template>
        <file-upload @send="sendFiles($event)"/>
    </el-drawer>
</template>

<script>
import { ElInput } from 'element-plus';
import Contenteditable from "../../Components/Contenteditable.vue";
import FileUpload from "../../Components/FileUpload.vue";
import { ElNotification } from 'element-plus'
import SettingsButton from "../../Components/SettingsButtons.vue";
import Field from "../../Components/Field.vue";
import {ChatDotSquare, Document, Paperclip, Bell, Edit, Share} from "@element-plus/icons-vue";
import {ref} from "vue";

export default {
    name: 'DetailDeal',
    components: {
        Contenteditable,
        FileUpload,
        SettingsButton,
        Field
    },
    props: {
        deal: {
            type: Object,
            required: true
        },
        pipelines: {
            type: Array,
            default: [{}]
        },
        stages: {
            type: Array,
            default: [{}]
        },
        dealFields: {
            type: Array,
            default: [{}]
        },
        clientFields: {
            type: Array,
            default: [{}]
        },
        users: {
            type: Array,
            default: [{}]
        },
        buttons: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            loading: false,
            active: ['1', '2'],
            visibleCommentForm: false,
            visibleFileUploadForm: false,

            client: this.deal?.client ?? {},
            pipeline: this.deal?.pipeline ?? {},
            responsible: this.deal?.responsible ?? {},
            stage: this.deal?.stage ?? {},
            comments: this.deal.comments ?? [],
            stageButtons: this.buttons ?? [],
            clientFields: this.clientFields ?? [],
            dealFields: this.dealFields ?? [],
            responsibles: this.users ?? [],
            deleteCommentId: 0,

            allFiles: this.deal?.comments?.reduce((acc, item) => {
                return [...acc,...item.files];
            }, []),
            allPipelines: this.pipelines ?? [],
            allStages: this.stages ?? [],

            files: [],
            newComment: { id: '', type: 'comment', content: '', author_id: null, files: [] },
        }
    },
    mounted() {
        this.deal.fields = this.dealFields;
        this.deal.client.fields = this.clientFields;
    },
    methods: {
        changePipeline(item) {
            this.send();
            axios
                .get('/deal/get_stages/' + item.id,)
                .then((response) => {
                    this.allStages = response.data;
                });
        },
        loadMore (e) {
            if (this.loading) {
                return;
            }

            let scrollPosition = this.$refs.scroll_container.$el.scrollHeight - this.$refs.scroll_container.$el.scrollTop;
            let elementHeigth = this.$refs.scroll_container.$el.offsetHeight;

            let can = false;

            scrollPosition = Math.floor(scrollPosition);

            if (scrollPosition === elementHeigth) {
                can = true;
            }

            if (scrollPosition === (elementHeigth - 1)) {
                can = true;
            }

            if (scrollPosition === (elementHeigth + 1)) {
                can = true;
            }

            if (can) {
                this.loading = true;
                axios.get('/deal/' + this.deal.id + '/load_comments?offset=' + this.comments.length).then((response) => {
                    this.comments = this.comments.concat(response.data.comments)
                    this.loading = false;
                })
            }
        },
        changeStage() {
            this.send();
        },
        sendComment(e) {
            this.visibleCommentForm = false;
            this.newComment.content = e.comment;
            if (this.newComment.content.length > 0) {
                this.comments.unshift(this.newComment);
            }
            this.prepareDataByButtonOptions(e.button.action);

            this.newComment = { id: '', type: 'comment', content: '', author_id: null, files: [] };
            this.send();
        },
        sendFiles(event) {
            this.visibleFileUploadForm = false;
            this.newComment.files = event;
            this.newComment.type = 'document';
            if (this.newComment.files.length > 0) {
                this.comments.unshift(this.newComment);
            }

            this.newComment = { id: '', type: 'comment', content: '', author_id: null, files: [] };
            this.send();
        },
        removeComment(comment) {
            this.comments.forEach((item, index) => {
                if (item.id === comment.id) {
                    this.comments.splice(index, 1);
                    this.deleteCommentId = item.id;
                }
            });

            this.send();
        },
        getUsers(query) {
            if (query.length >= 3) {
                axios.get('/admin/user/find-users?user_name=' + query)
                    .then((response) => {
                        this.responsibles = response.data.data;
                    });
            }
        },
        changeData(options) {
            this.prepareDataByButtonOptions(options);
            this.send();
        },
        prepareDataByButtonOptions(action) {
            this.pipeline.id = !!action.pipeline_id ? action.pipeline_id : this.pipeline.id;
            this.stage.id = !!action.stage_id ? action.stage_id : this.stage.id;
            this.responsible.id = !!action.responsible_id ? action.responsible_id : this.responsible.id;
        },
        send() {
            const formData = new FormData();
            formData.append('id', this.deal.id);
            formData.append('name', this.deal.name);
            formData.append('pipeline_id', this.pipeline.id);
            formData.append('stage_id', this.stage.id);
            formData.append('responsible_id', this.responsible.id);
            formData.append('client_id', this.deal.client_id);

            formData.append('comment_count', this.comments.length ?? 0);
            formData.append('delete_comment_id', this.deleteCommentId);

            this.deal.fields = this.deal.fields ?? [];
            this.deal?.fields.forEach((field, fieldIndex) => {
                formData.append('fields[' + field.id + '][value]', field.pivot?.value ?? '');
            });

            this.deal.client = this.deal.client ?? [];
            formData.append('client[name]', this.deal.client?.name);
            this.deal.client.fields = this.deal.client?.fields ?? [];
            this.deal.client?.fields.forEach((field, fieldIndex) => {
                formData.append('client[fields][' + field.id + '][value]', field.pivot?.value ?? '');
            });

            this.deal.comments = this.comments ?? [];
            this.deal?.comments.forEach((comment, commentIndex) => {
                if (!comment.id) {
                    formData.append('new_comment[id]', comment.id ?? '');
                    formData.append('new_comment[deal_id]', this.deal.id);
                    formData.append('new_comment[type]', comment.type);
                    formData.append('new_comment[content]', comment.content);
                    comment.files = comment.files ?? [];
                    comment.files.forEach((file, fileIndex) => {
                        formData.append('new_comment[files][' + fileIndex + ']', file);
                    })
                } else {
                    formData.append('comments[' + commentIndex + '][id]', comment.id ?? '');
                    formData.append('comments[' + commentIndex + '][deal_id]', this.deal.id);
                    formData.append('comments[' + commentIndex + '][type]', comment.type);
                    formData.append('comments[' + commentIndex + '][content]', comment.content);
                    comment.files = comment.files ?? [];
                    comment.files.forEach((file, fileIndex) => {
                        formData.append('comments[' + commentIndex + '][files][' + fileIndex + ']', file);
                    })
                }
            });

            console.log( this.deal)
            axios
                .post('/deal/update',  formData)
                .then((response) => {
                    console.log(response.data.deal);
                    this.comments = response.data.deal.comments;
                    this.stage = response.data.deal.stage;
                    this.allStages = response.data.stages;
                    this.allPipelines = response.data.pipelines;
                    this.stageButtons = response.data.deal.pipeline.buttons;
                    this.responsible = response.data.deal.responsible;
                    this.responsibles = [this.responsible];
                    console.log(this.stageButtons);
                    ElNotification({
                        title: 'Сохранено',
                        type: 'success',
                        position: 'bottom-right',
                    });
                }
            )
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
        definitionCommentColor(comment) {
            switch (comment.type) {
                case 'document':
                    return 'grey';
                case 'action':
                    return 'pink';
                default:
                    return 'green';
            }
        }
    }
}
</script>
<style scoped>
.test{
    width: 1000px;
}
.wrap {
    display:flex;
    flex-direction: row;
}
.card {
    display: flex;
    flex-direction: row;
    gap: 10px;
    width: calc(100% - 200px);
}
.card-left {
    min-width: 55%;
    padding: 8px;
}
.card-body {
    justify-content: flex-end;
}
.card-right {
    width: inherit;
}
.flex-column {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.flex-inline {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 10px;
}
.comment-row {
    display: flex;
    width: 100%;
    gap: 10px;
    min-height: 100px;
    padding: 16px;
}

.document_container {
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.document_item {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}
.custom-field__container {
    display: flex;
    flex-direction: row;
    gap: 10px;
    justify-content: space-between;
}


.infinite-list {
    min-height: 700px;
    max-height: 700px;
    width: 100%;
    padding: 0;
    margin: 0;
    list-style: none;
}
</style>
