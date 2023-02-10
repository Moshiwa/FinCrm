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
                                            v-for="pipeline in pipelines"
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
                                            v-for="stage in stages"
                                            :key="stage.id"
                                            :label="stage.name"
                                            :value="stage"
                                        />
                                    </el-select>
                                </div>
                            </el-descriptions-item>
                            <el-descriptions-item label="Ответственный">
                                <a href="">{{ deal.responsible?.name }}</a>
                            </el-descriptions-item>
                            <el-descriptions-item v-for="field in dealFields" :label="field.name">
                                <div v-if="field.type === 'select'">
                                    <el-select
                                        v-model="field.pivot.value"
                                        @change="send"
                                    >
                                        <el-option
                                            v-for="(option, index) in field.options"
                                            :key="index"
                                            :label="option.value"
                                            :value="option.value"
                                        />
                                    </el-select>
                                </div>
                                <div v-else-if="field.type === 'date'">
                                    <el-input
                                        v-model="field.pivot.value"
                                        type="date"
                                        @change="send"
                                    />
                                </div>
                                <contenteditable
                                    v-else
                                    v-model="field.pivot.value"
                                    @send="send"
                                />
                            </el-descriptions-item>
                            <el-descriptions-item label="Добавить">
                                <selected-field
                                    :fields="fields"
                                    @updateField="addNewDealField($event)"
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
                                <div v-if="field.type === 'select'">
                                    <el-select
                                        v-model="field.pivot.value"
                                        @change="send"
                                    >
                                        <el-option
                                            v-for="(option, index) in field.options"
                                            :key="index"
                                            :label="option"
                                            :value="option"
                                        />
                                    </el-select>
                                </div>
                                <div v-else-if="field.type === 'date'">
                                    <el-input
                                        v-model="field.pivot.value"
                                        type="date"
                                        @change="send"
                                    />
                                </div>
                                <contenteditable
                                    v-else
                                    v-model="field.pivot.value"
                                    @send="send"
                                />
                            </el-descriptions-item>
                            <el-descriptions-item label="Добавить">
                                <selected-field
                                    :fields="fields"
                                    @updateField="addNewClientField($event)"
                                />
                            </el-descriptions-item>
                        </el-descriptions>
                    </el-collapse-item>
                </el-collapse>

                <el-collapse v-model="active">
                    <el-collapse-item title="Документы" name="3">
                        <div class="document_container" v-for="file in allFiles">
                            <div class="document_item">
                                <div class="document-date">
                                    {{ file.created_at }}
                                </div>
                                <div class="document-name">
                                    {{ file.original_name }}
                                </div>
                                <div class="document-actions">
                                    <el-button-group class="ml-4">
                                        <el-button type="primary" icon="" />
                                        <el-button type="primary" icon="" />
                                    </el-button-group>
                                </div>
                            </div>
                        </div>
                    </el-collapse-item>
                </el-collapse>
            </div>



            <div class="card-right">
                <div class="card-body row">
                    <el-timeline>
                        <div
                            class="comment-container"
                            v-for="comment in comments"
                        >
                            <el-timeline-item
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
                                            <!--                    Здесь было удаление-->
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
                        </div>
                    </el-timeline>
                </div>
            </div>
        </div>

        <settings-button
            :settings="stage.settings"

            @commentSend="sendComment($event)"
            @fileUploadSend="sendFiles($event)"
        />
    </div>
</template>

<script>
import { ElInput } from 'element-plus';
import SelectedField from "./Components/SelectedField.vue"
import Contenteditable from "./Components/Contenteditable.vue";
import FileUpload from "./Components/FileUpload.vue";
import { ElNotification } from 'element-plus'
import SettingsButton from "./Components/SettingsButtons.vue";
import {ChatDotSquare, Document, Paperclip, Bell, Edit, Share} from "@element-plus/icons-vue";

export default {
    name: 'DetailDeal',
    components: {SelectedField, Contenteditable, FileUpload, SettingsButton},
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
        fields: {
            type: Array,
            default: [{}]
        },
    },
    data() {
        return {
            active: ['1'],
            visibleCommentForm: false,
            visibleFileUploadForm: false,

            client: this.deal?.client ?? {},
            pipeline: this.deal?.pipeline ?? {},
            responsible: this.deal?.responsible ?? {},
            stage: this.deal?.stage ?? {},
            stages: this.stages ?? [],
            comments: this.deal.comments ?? [],
            clientFields: this.deal.client?.fields ?? [],
            dealFields: this.deal.fields ?? [],

            deleteCommentId: 0,

            allFiles: this.deal.comments.reduce((acc, item) => {
                return [...acc,...item.files];
            }, []),

            files: [],
            newComment: { id: '', type: 'comment', content: '', author_id: null, files: [] },
        }
    },
    mounted() {
        console.log(this.deal);
    },
    methods: {
        changePipeline(item) {
            this.send();
            axios
                .get('/deal/get_stages/' + item.id,)
                .then((response) => {
                    this.stages = response.data;
                });
        },
        changeStage() {
            this.send();
        },
        addNewClientField(event) {
            let newField = {
                id: event.field.id,
                options: event.field.options,
                type: event.field.type,
                name: event.field.name,
                pivot: {
                    client_id: this.client.id,
                    field_id: event.field.id,
                    value: event.value
                }
            }

            this.clientFields.push(newField);
            this.send();
        },
        addNewDealField(event) {
            let newField = {
                id: event.field.id,
                options: event.field.options,
                type: event.field.type,
                name: event.field.name,
                pivot: {
                    client_id: this.client.id,
                    field_id: event.field.id,
                    value: event.value
                }
            }

            this.dealFields.push(newField);
            this.send();
        },
        sendComment(e) {
            this.visibleCommentForm = false;
            this.newComment.content = e;
            if (this.newComment.content.length > 0) {
                this.comments.unshift(this.newComment);
            }

            this.send();
        },
        sendFiles(event) {
            this.visibleFileUploadForm = false;
            this.newComment.files = event;
            this.newComment.type = 'document';
            if (this.newComment.files.length > 0) {
                this.deal.comments.unshift(this.newComment);
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
        send() {
            const formData = new FormData();
            formData.append('id', this.deal.id);
            formData.append('name', this.deal.name);
            formData.append('pipeline_id', this.pipeline.id);
            formData.append('stage_id', this.stage.id);
            formData.append('responsible_id', this.deal.responsible_id);
            formData.append('client_id', this.deal.client_id);

            formData.append('delete_comment_id', this.deleteCommentId);

            this.deal.fields = this.deal.fields ?? [];
            this.deal?.fields.forEach((field, fieldIndex) => {
                formData.append('fields[' + field.id + '][value]', field.pivot?.value ?? '');
            });

            this.deal.client = this.deal.client ?? [];
            formData.append('client[name]', this.deal.client?.name);
            this.deal.client?.fields.forEach((field, fieldIndex) => {
                formData.append('client[fields][' + field.id + '][value]', field.pivot?.value ?? '');
            });

            this.deal.comments = this.comments ?? [];
            this.deal?.comments.forEach((comment, commentIndex) => {
                formData.append('comments[' + commentIndex + '][id]', comment.id ?? '');
                formData.append('comments[' + commentIndex + '][deal_id]', this.deal.id);
                formData.append('comments[' + commentIndex + '][type]', comment.type);
                formData.append('comments[' + commentIndex + '][content]', comment.content);
                comment.files = comment.files ?? [];
                comment.files.forEach((file, fileIndex) => {
                    formData.append('comments[' + commentIndex + '][files][' + fileIndex + ']', file);
                })
            });

            console.log( this.deal)
            axios
                .post('/deal/update',  formData)
                .then((response) => {
                    this.comments = response.data.comments;
                    this.stage = response.data.stage;
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
</style>
