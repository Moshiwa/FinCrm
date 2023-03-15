<template>
    <div class="row">
        <div class="card row common-gap">
            <div class="card-left">
                <el-divider content-position="center">ЗАДАЧА</el-divider>
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
                        @change="send"
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
                        @change="send"
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
                        @change="send"
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
                        @change="send"
                    >
                        <el-option
                            v-for="user in users"
                            :key="user.id"
                            :label="user.name"
                            :value="user"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item label="Даты начала/окончания" class="select-container">
                    <el-date-picker
                        v-model="datetime"
                        type="datetimerange"
                        format="YYYY-MM-DD hh:mm"
                        value-format="YYYY-MM-DD hh:mm"
                        :shortcuts="shortcuts"
                        @change="changeDateTime"
                    />
                </el-form-item>

                <el-divider content-position="left">Дополнительные поля</el-divider>
                <el-form-item
                    v-for="field in thisTask.all_fields"
                    :label="field.name"
                    :required="field.is_required"
                >
                    <field
                        :field="field"
                        @send="send"
                    />
                </el-form-item>
                <div>
                    <a href="/admin/field/create?entity=task">Добавить поле</a>
                </div>
            </div>

            <div class="card-right">
                <filters :filter="filter" />
                <div class="card-body row">
                    <el-timeline class="infinite-list">
                        <comments
                            :comments="thisTask.comments"
                            :auth="auth"
                            @commentSend="prepareCommentDataSend($event)"
                        />
                        <div v-loading="loading"></div>
                    </el-timeline>
                </div>
            </div>
        </div>

        <action-buttons
            :buttons="stageButtons"
            :stage="thisTask.stage"
            :is-delete="permissions.can_delete"
            :is-upload="true"
            @commentSend="sendComment($event)"
            @filesSend="sendFiles($event)"
            @changeData="changeData($event)"
            @deleteAction="deleteTask"
        />
    </div>

</template>

<script>
import {ElInput, ElMessageBox} from 'element-plus';
import Contenteditable from "../../Components/Contenteditable.vue";
import FileUpload from "../../Components/FileUpload.vue";
import { ElNotification } from 'element-plus'
import ActionButtons from "../../Components/ActionButtons.vue";
import Helper from "../../Mixins/Helper.vue";
import Comments from "../../Components/Comments.vue";
import Field from "../../Components/Field.vue";
import Filters from "../../Components/Filters.vue";

export default {
    name: 'DetailTask',
    components: {
        Contenteditable,
        FileUpload,
        ActionButtons,
        Field,
        Comments,
        Filters
    },
    mixins: [
        Helper,
    ],
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
        users: {
            type: Array,
            default: [{}]
        },
        buttons: {
            type: Array,
            default: []
        },
        filter: {
            type: Array,
            required: false,
        }
    },
    data() {
        return {
            loading: false,
            visibleCommentForm: false,
            visibleFileUploadForm: false,
            shortcuts: [
                {
                    text: 'Сегодня',
                    value: new Date(),
                },
                {
                    text: 'Вчера',
                    value: () => {
                        const date = new Date()
                        date.setTime(date.getTime() - 3600 * 1000 * 24)
                        return date
                    },
                },
                {
                    text: 'Неделю назад',
                    value: () => {
                        const date = new Date()
                        date.setTime(date.getTime() - 3600 * 1000 * 24 * 7)
                        return date
                    },
                },
            ],
            thisTask: this.task,
            datetime: [ this.task.start, this.task.end ],

            allStages: this.stages ?? [],
            stageButtons: this.buttons ?? [],
            users: this.users ?? [],

            deleteCommentId: 0,
            action: {},

            newComment: { id: '', type: 'comment', content: '', author_id: null, files: [] },

            permissions: {
                can_delete: this.auth.permission_names.find((item) => item === 'tasks.delete'),
            }
        }
    },
    beforeMount() {
        console.log(this.auth);
        console.log(this.thisTask);
        $(document).on('scroll', this.loadMore);
        this.thisTask.all_fields = this.castFieldValue(this.thisTask.all_fields);
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

                let url = '/admin/task/' + this.thisTask.id + '/load_comments?';
                url += window.location.search
                if (this.thisTask.comments?.length > 0) {
                    url+= '&offset=' +  this.thisTask.comments.length;
                }

                axios.get(url).then((response) => {
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
            this.deleteCommentId = null;
        },
        changeData(options) {
            this.prepareDataByButtonOptions(options);
            this.send();
        },
        changeDateTime() {
            let times = [];
            if (this.datetime?.length > 0) {
                this.datetime.forEach((date) => {
                    times.push(date);
                });
            }

            this.thisTask.start = times[0] ?? this.thisTask.start;
            this.thisTask.end = times[1] ?? this.thisTask.end;
            this.send();
        },
        prepareDataByButtonOptions(action) {
            this.action = action;
            this.thisTask.stage.id = !!action.task_stage_id ? action.task_stage_id : this.thisTask.stage?.id;
            this.thisTask.responsible = !!action.responsible_id ? {id: action.responsible_id} : this.thisTask.responsible;
            this.thisTask.manager = !!action.manager_id ? {id: action.manager_id} : this.thisTask.manager;
            this.thisTask.executor = !!action.executor_id ? {id: action.executor_id} : this.thisTask.executor;
            this.thisTask.start = !!action.start_time ? action.start_time : this.thisTask.start;
            this.thisTask.end = !!action.end_time ? action.end_time : this.thisTask.end;
        },
        deleteTask() {
            ElMessageBox.confirm(
                'Вы уверены?',
                'Удалить задачу',
                {
                    confirmButtonText: 'Хорошо',
                    cancelButtonText: 'Отмена',
                    type: 'warning',
                }
            )
            .then(() => {
                axios
                    .delete('/admin/task/' + this.thisTask.id)
                    .then((response) => {
                        location.href = '/admin/task/';
                    })
                    .catch((response) => {
                        if (!!response.response?.data?.errors) {
                            response.response.data.errors.forEach((error) => {
                                ElNotification({
                                    title: error,
                                    type: 'error',
                                    position: 'bottom-right',
                                });
                            });
                        }
                    });
                }
            );
        },
        send() {
            const formData = new FormData();
            if (!!this.thisTask.id) {
                formData.append('id', this.thisTask.id);
            }
            if (!!this.thisTask.name) {
                formData.append('name', this.thisTask.name);
            }
            if (!!this.thisTask.description) {
                formData.append('description', this.thisTask.description);
            }
            if (!!this.thisTask.start) {
                formData.append('start', this.thisTask.start);
            }
            if (!!this.thisTask.end) {
                formData.append('end', this.thisTask.end);
            }
            if (!!this.thisTask.stage?.id) {
                formData.append('task_stage_id', this.thisTask.stage.id);
            }
            if (!!this.thisTask.responsible?.id) {
                formData.append('responsible_id', this.thisTask.responsible.id);
            }
            if (!!this.thisTask.manager?.id) {
                formData.append('manager_id', this.thisTask.manager.id);
            }
            if (!!this.thisTask.executor?.id) {
                formData.append('executor_id', this.thisTask.executor.id);
            }

            formData.append('comment_count', this.thisTask.comments.length ?? 0);
            if (!!this.deleteCommentId) {
                formData.append('delete_comment_id', this.deleteCommentId);
            }

            this.thisTask.all_fields = this.thisTask.all_fields ?? [];
            this.thisTask?.all_fields.forEach((field, fieldIndex) => {
                formData.append('fields[' + field.id + '][value]', field.pivot?.value ?? '');
            });

            this.thisTask.comments = this.thisTask.comments ?? [];
            this.thisTask?.comments.forEach((comment, commentIndex) => {
                if (!comment.id) {
                    formData.append('new_comment[id]', comment.id ?? '');
                    formData.append('new_comment[task_id]', this.thisTask.id);
                    formData.append('new_comment[type]', comment.type);
                    formData.append('new_comment[content]', comment.content);
                    comment.files = comment.files ?? [];
                    comment.files.forEach((file, fileIndex) => {
                        formData.append('new_comment[files][' + fileIndex + ']', file);
                    })
                }
            });

            let url = '/admin/task/update';
            url += window.location.search

            axios
                .post(url,  formData)
                .then((response) => {
                    console.log(response.data.task);
                    this.thisTask = response.data.task;
                    this.allStages = response.data.stages;
                    this.users = response.data.users;
                    this.stageButtons = response.data.task.stage.buttons;
                    this.action = null;

                    this.thisTask.fields = this.castFieldValue(this.thisTask.fields);

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
</style>
