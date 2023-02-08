<template>
    <div class="wrap">
        <div class="card">
            <div class="card-left">
                <el-descriptions
                    direction="vertical"
                    title="Общее"
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
                            @updateField="addNewDealField($event)"
                        />
                    </el-descriptions-item>
<!--                    <el-descriptions-item label="Комментарий">
                        <contenteditable
                            v-model="deal.comment"
                            @send="send"
                        />
                    </el-descriptions-item>-->
                </el-descriptions>
                <el-descriptions
                    direction="vertical"
                    title="Клиент"
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
            </div>

            <div class="card-right">
                <div class="card-body row">

                    <div
                        class="inline-flex comment-row"
                        v-for="comment in comments"
                    >
                        <div class="row-left">
                            <el-icon>
                                <ChatDotSquare v-if="comment.type === 'comment'"/>
                                <Document v-if="comment.type === 'document'"/>
                            </el-icon>
                        </div>
                        <div class="row-right">
                            <div class="row-right__upper">
                                <div class="row-right__title">
                                    <div class="row-right__type-name">
                                        {{ definitionCommentType(comment.type) }}
                                    </div>
                                    <div class="row-right__date"> {{ comment.updated_at }}</div>
                                </div>
                                <div class="row-right__delete" @click="removeComment(comment)">
                                    x
                                </div>
                            </div>
                            <div class="row-right__lower">
                                <div class="row-right__content">
                                    <contenteditable
                                        v-if="comment.type === 'comment'"
                                        v-model="comment.content"
                                        @send="send"
                                    />
                                    <div
                                        v-else-if="comment.type === 'document'"
                                        class="flex-inline"
                                    >
                                        <el-image
                                            v-for="file in comment.files"
                                            style="width: 100px; height: 100px"
                                            :src="file.full_path"
                                            :zoom-rate="1.2"
                                            :preview-src-list="[file.full_path]"
                                            :initial-index="4"
                                            fit="cover"
                                        />
                                    </div>
                                </div>
                                <div class="row-right__author">
                                    {{ comment.author?.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body flex-column">
            <div class="w-100">
                <el-button class="w-100" @click="visibleCommentForm = true">Оставить комментарий</el-button>
            </div>
            <div class="w-100">
                <el-button class="w-100" @click="visibleFileUploadForm = true">Прикрепить файл</el-button>
            </div>
            <hr>
        </div>
    </div>

    <el-drawer v-model="visibleCommentForm" :show-close="false">
        <template #header="{ close, titleId, titleClass }">
            <h4 :id="titleId" :class="titleClass">Оставте комментарий</h4>
        </template>
        <el-input
            v-model="newComment.content"
            rows="5"
            type="textarea"
        />
        <el-button
            class="w-100 mt-3"
            type="success"
            @click="sendComment"
        >
            Отправить
        </el-button>
    </el-drawer>

    <el-drawer v-model="visibleFileUploadForm" :show-close="false">
        <template #header="{ close, titleId, titleClass }">
            <h4 :id="titleId" :class="titleClass">Выберите файлы</h4>
        </template>
        <file-upload @send="sendFiles($event)"/>
    </el-drawer>

</template>

<script>
import { ElInput } from 'element-plus';
export default {
    name: 'DetailDeal',
    props: {
        deal: {
            type: [Array, Object],
            default: null
        },
        pipelines: {
            type: Array,
            default: null
        },
        stages: {
            type: Array,
            default: [{}]
        },
        fields: {
            type: Array,
            default: [{}]
        }
    },
    mounted() {
        console.log(this.deal)
        console.log(this.pipelines);
    },
    computed: {
        clientName() {
            return this.deal.client?.name
        },
        stage() {
            return this.deal?.stage;
        }
    },
    data() {
        return {
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

            files: [],
            newComment: { type: 'comment', content: '', author_id: null, files: [] },
        }
    },
    methods: {
        changePipeline(item) {
            this.send();
            axios
                .get('/deal/get_stages/' + item.id,)
                .then((response) => {
                    this.stages = response.data;
                    this.stage = {};
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
        sendComment() {
            this.visibleCommentForm = false;
            if (this.newComment.content.length > 0) {
                this.deal.comments.unshift(this.newComment);
            }

            this.send();
            this.newComment = { type: 'comment', content: '', author_id: null, files: [] };
        },
        sendFiles(event) {
            this.visibleFileUploadForm = false;
            this.newComment.files = event;
            this.newComment.type = 'document';
            if (this.newComment.files.length > 0) {
                this.deal.comments.unshift(this.newComment);
            }

            this.send();
            this.newComment = { type: 'comment', content: '', author_id: null, files: [] };
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
            /*this.deal.client = this.client;
            this.deal.pipeline_id = this.pipeline.id;
            this.deal.responsible = this.responsible;
            this.deal.stage_id = this.stage.id;
            this.deal.comments = this.comments;
            if (this.newComment.content.length > 0 || this.newComment.files) {
                this.deal.comments.unshift(this.newComment);
            }*/


            const formData = new FormData();
            formData.append('id', this.deal.id);
            formData.append('name', this.deal.name);
            formData.append('pipeline_id', this.deal.pipeline_id);
            formData.append('stage_id', this.deal.stage_id);
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

            this.deal.comments = this.deal?.comments ?? [];
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
            axios.post('/deal/update',  formData)
        },
        definitionCommentType(text) {
            switch (text) {
                case 'text':
                    return 'Комментарий'
                case 'audio':
                    return 'Аудиозапись'
                case 'document':
                    return 'Документ'
                case 'image':
                    return 'Изображение'
            }
        }
    }
}
</script>
<style scoped>
.wrap {
    display:flex;
    flex-direction: row;
}
.card {
    display: flex;
    flex-direction: row;
    gap: 10px;
    width: 100%;
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
.bold-labels {
    padding-left: 0px;
}
.row-label {
    min-width: 125px;
}
.comment-row {
    display: flex;
    width: 100%;
    gap: 10px;
    border-bottom: 1px solid #b6b6b6;
    max-height: 160px;
    min-height: 160px;
    padding: 16px;
}
.row-right {
    display: flex;
    flex-direction: column;
    width: 100%;
    justify-content: space-between;
}
.row-right__title {
    display: flex;
    flex-direction: row;
    gap: 10px;
}
.row-right__upper {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
.row-right__lower {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: end;
}
.row-right__delete {
    cursor: pointer;
}
</style>
