<template>
    <div class="wrap">
        <div class="card">
            <div class="card-left">
                <el-input
                    class="input-title hidden-border"
                    v-model="thisTask.name"
                    @change="send"
                />
                <el-form-item label="Описание">
                    <el-input
                        type="textarea"
                        v-model="thisTask.description"
                        @change="send"
                    />
                </el-form-item>
                <el-form-item label="Статус">
                    <el-select
                        v-model="thisTask.stage"
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
                </el-form-item>
                <el-form-item label="Ответственный">
                    <el-select
                        v-model="thisTask.responsible"
                        value-key="id"
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Please enter a keyword"
                        @change="changeResponsible"
                    >
                        <el-option
                            v-for="user in users"
                            :key="user.id"
                            :label="user.name"
                            :value="user"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="Наблюдатель">
                    <el-select
                        v-model="thisTask.manager"
                        value-key="id"
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Please enter a keyword"
                        @change="changeManager"
                    >
                        <el-option
                            v-for="user in users"
                            :key="user.id"
                            :label="user.name"
                            :value="user"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="Исполнитель">
                    <el-select
                        v-model="thisTask.executor"
                        value-key="id"
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Please enter a keyword"
                        @change="changeExecutor"
                    >
                        <el-option
                            v-for="user in users"
                            :key="user.id"
                            :label="user.name"
                            :value="user"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item label="Дата начала">
                    <el-date-picker
                        v-model="thisTask.start"
                        type="datetime"
                        placeholder="Select date and time"
                        :shortcuts="shortcuts"
                    />
                </el-form-item>
                <el-form-item label="Дата окончания">
                    <el-date-picker
                        v-model="thisTask.end"
                        type="datetime"
                        placeholder="Select date and time"
                        :shortcuts="shortcuts"
                    />
                </el-form-item>

                <el-collapse v-model="active">
                    <el-collapse-item title="Дополнительные поля" name="1">
                        <el-form-item
                            v-for="field in thisTask.fields"
                            :label="field.name"
                            :required="field.is_required"
                        >
                            <field
                                :field="field"
                                @send="send"
                            />
                        </el-form-item>
                    </el-collapse-item>
                </el-collapse>
            </div>

            <div class="card-right">
                <div class="card-body row">
                    <el-button @click="visibleFileUploadForm = true">Прикрепить документ</el-button>
                    <el-timeline class="infinite-list">
                        <comments
                            :comments="thisTask.comments"
                            :auth="auth"
                            @commentSend="prepareCommentDataSend($event)"
                        />
                    </el-timeline>
                </div>
            </div>
        </div>

        <action-buttons
            :buttons="stageButtons"
            :stage="thisTask.stage"
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
import ActionButtons from "../../Components/ActionButtons.vue";
import Comments from "../../Components/Comments.vue";
import Field from "../../Components/Field.vue";

export default {
    name: 'DetailDeal',
    components: {
        Contenteditable,
        FileUpload,
        ActionButtons,
        Field,
        Comments
    },
    props: {
        auth: {
            type: Object,
            required: true
        },
        task: {
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
        taskFields: {
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
            active: ['1'],
            visibleCommentForm: false,
            visibleFileUploadForm: false,
            shortcuts: [
                {
                    text: 'Today',
                    value: new Date(),
                },
                {
                    text: 'Yesterday',
                    value: () => {
                        const date = new Date()
                        date.setTime(date.getTime() - 3600 * 1000 * 24)
                        return date
                    },
                },
                {
                    text: 'A week ago',
                    value: () => {
                        const date = new Date()
                        date.setTime(date.getTime() - 3600 * 1000 * 24 * 7)
                        return date
                    },
                },
            ],
            thisTask: this.task,

            allStages: this.stages ?? [],
            stageButtons: this.buttons ?? [],
            users: this.users ?? [],

            deleteCommentId: 0,
            action: {},

            newComment: { id: '', type: 'comment', content: '', author_id: null, files: [] },
        }
    },
    mounted() {
        $(document).on('scroll', this.loadMore);
        console.log(this.thisTask);
        this.thisTask.fields = this.taskFields;
    },
    methods: {
        changeStage(item) {
            this.action = { stage_id: item.id };

            this.send();
        },
        changeResponsible(item) {
            this.action = { responsible_id: item.id }
            this.send();
        },
        changeManager(item) {
            this.action = { manager_id: item.id }
            this.send();
        },
        changeExecutor(item) {
            this.action = { executor_id: item.id }
            this.send();
        },
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
                axios.get('/admin/task/' + this.thisTask.id + '/load_comments?offset=' + this.thisTask.comments.length).then((response) => {
                    this.thisTask.comments = this.thisTask.comments.concat(response.data.comments)
                    this.loading = false;
                })
            }
        },
        sendComment(e) {
            this.visibleCommentForm = false;
            this.newComment.content = e.comment;
            if (this.newComment.content.length > 0) {
                this.thisTask.comments.unshift(this.newComment);
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
                this.thisTask.comments.unshift(this.newComment);
            }

            this.newComment = { id: '', type: 'comment', content: '', author_id: null, files: [] };
            this.send();
        },
        prepareCommentDataSend(event) {
            if (event) {
                this.deleteCommentId = event;
            }
            this.send();
        },
        changeData(options) {
            this.prepareDataByButtonOptions(options);
            this.send();
        },
        prepareDataByButtonOptions(action) {
            this.action = action;
            this.thisTask.stage.id = !!action.stage_id ? action.stage_id : this.thisTask.stage.id;
            /*this.thisTask.pipeline.id = !!action.pipeline_id ? action.pipeline_id : this.thisDeal.pipeline.id;
            this.thisTask.stage.id = !!action.stage_id ? action.stage_id : this.thisDeal.stage.id;
            this.thisTask.responsible.id = !!action.responsible_id ? action.responsible_id : this.thisDeal.responsible.id;*/
        },
        send() {
            const formData = new FormData();
            formData.append('id', this.thisTask.id);
            formData.append('name', this.thisTask.name);
            formData.append('description', this.thisTask.description);
            formData.append('task_stage_id', this.thisTask.stage.id);
            formData.append('responsible_id', this.thisDeal.responsible.id);
            formData.append('client_id', this.thisDeal.client_id);

            formData.append('comment_count', this.thisDeal.comments.length ?? 0);
            formData.append('delete_comment_id', this.deleteCommentId);

            if (!!this.action) {
                if (!!this.action.id) {
                    formData.append('action[id]', this.action.id ?? null);
                }

                if (!!this.action.pipeline_id) {
                    formData.append('action[pipeline_id]', this.action.pipeline_id ?? null);
                }

                if (!!this.action.stage_id) {
                    formData.append('action[stage_id]', this.action.stage_id ?? null);
                }

                if (!!this.action.responsible_id) {
                    formData.append('action[responsible_id]', this.action.responsible_id ?? null);
                }

                if (!!this.action.comment) {
                    formData.append('action[comment]', this.action.comment ?? false);
                }
            }

            this.thisDeal.fields = this.thisDeal.fields ?? [];
            this.thisDeal?.fields.forEach((field, fieldIndex) => {
                formData.append('fields[' + field.id + '][value]', field.pivot?.value ?? '');
            });

            this.thisDeal.client = this.thisDeal.client ?? [];
            formData.append('client[name]', this.thisDeal.client?.name);
            this.thisDeal.client.fields = this.thisDeal.client?.fields ?? [];
            this.thisDeal.client?.fields.forEach((field, fieldIndex) => {
                formData.append('client[fields][' + field.id + '][value]', field.pivot?.value ?? '');
            });

            this.thisDeal.comments = this.thisDeal.comments ?? [];
            this.thisDeal?.comments.forEach((comment, commentIndex) => {
                if (!comment.id) {
                    formData.append('new_comment[id]', comment.id ?? '');
                    formData.append('new_comment[deal_id]', this.thisDeal.id);
                    formData.append('new_comment[type]', comment.type);
                    formData.append('new_comment[content]', comment.content);
                    comment.files = comment.files ?? [];
                    comment.files.forEach((file, fileIndex) => {
                        formData.append('new_comment[files][' + fileIndex + ']', file);
                    })
                }
            });

            axios
                .post('/admin/deal/update',  formData)
                .then((response) => {
                    this.thisDeal = response.data.deal;
                    this.allStages = response.data.stages;
                    this.allPipelines = response.data.pipelines;
                    this.responsibles = [this.thisDeal.responsible];
                    this.stageButtons = response.data.deal.pipeline.buttons;
                    this.action = null;
                    ElNotification({
                        title: 'Сохранено',
                        type: 'success',
                        position: 'bottom-right',
                    });
                }
            )
        },
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
    width: calc(100% - 200px);
}
.input-title {
    margin: 10px 0 10px 0;
    height: 40px;
    font-size: 30px;
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
.infinite-list {
    min-height: 700px;
    width: 100%;
    padding: 0;
    margin: 0;
    list-style: none;
}
</style>
