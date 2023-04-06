<template>
    <div class="row">
        <div class="card row common-gap">
            <div class="card-left">
                <el-input
                    class="input-title hidden-border"
                    :disabled="!permissions.can_update_task"
                    v-model="thisTask.name"
                    @change="send"
                />
                <div :class="this.taskIsOverdue ? 'entity-state title-overdue' : 'entity-state'">
                    Необходимо закрыть сделку до {{ thisTask.string_deadline }}
                </div>
                <el-divider content-position="center">ЗАДАЧА</el-divider>
                <el-form-item label="Описание">
                    <el-input
                        type="textarea"
                        :disabled="!permissions.can_update_task"
                        v-model="thisTask.description"
                        @change="send"
                    />
                </el-form-item>
                <el-form-item label="Стадия">
                    <el-select
                        v-model="thisTask.stage"
                        :disabled="!permissions.can_change_stage"
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
                        :disabled="!(permissions.can_update_task && (permissions.can_change_responsible || permissions.can_change_members_self))"
                        value-key="id"
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Выберите ответственного"
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
                        :disabled="!(permissions.can_change_members_self || permissions.can_update_task)"
                        value-key="id"
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Выберите наблюдателя"
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
                        :disabled="!(permissions.can_change_members_self || permissions.can_update_task)"
                        value-key="id"
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Выберите исполнителя"
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

                <el-form-item label="Срок до">
                    <el-date-picker
                        v-model="thisTask.deadline"
                        :disabled="!(permissions.can_update_task || permissions.can_change_members_self)"
                        type="datetime"
                        placeholder="Выберите дату и время"
                        format="YYYY/MM/DD HH:mm"
                        :clearable="false"
                        value-format="YYYY-MM-DD H:m"
                        @change="send"
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
                        :disabled="!permissions.can_update_task"
                        @send="changeCustomField(field)"
                    />
                </el-form-item>
                <div v-if="permissions.can_create_field">
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
                        <a
                            href="javascript:void(0)"
                            class="d-print-none font-sm load-more"
                            @click="loadMore"
                        >
                            Загрузить еще
                        </a>
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
import FileUpload from "../../Components/FileUpload.vue";
import { ElNotification } from 'element-plus'
import ActionButtons from "../../Components/ActionButtons.vue";
import Helper from "../../Mixins/Helper.vue";
import Comments from "../../Components/Comments.vue";
import Field from "../../Components/Field.vue";
import Filters from "../../Components/Filters.vue";
import DataGenerateHelper from "../../Mixins/DataGenerateHelper.vue";

export default {
    name: 'DetailTask',
    components: {
        FileUpload,
        ActionButtons,
        Field,
        Comments,
        Filters
    },
    mixins: [
        Helper, DataGenerateHelper
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
            defaultResponsible: this.task.responsible ?? {},
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
                can_change_stage: this.auth.permission_names.find((item) => item === 'tasks.change_stage'),
                can_change_responsible: this.auth.permission_names.find((item) => item === 'tasks.change_responsible'),
                can_create_field: this.auth.permission_names.find((item) => item === 'fields.create'),
                can_update_task: this.auth.permission_names.find((item) => item === 'tasks.update'),
                can_change_members_self: (this.auth.permission_names.find((item) => item === 'tasks.change_members_self')) !== undefined ? this.auth.id === this.task.responsible_id : false,
            }
        }
    },
    beforeMount() {
        this.thisTask.all_fields = this.castFieldValue(this.thisTask.all_fields);
    },
    computed: {
        taskIsOverdue() {
            let deadline = new Date(this.thisTask.deadline).getTime();
            let now = Date.now();
            return deadline < now;
        }
    },
    methods: {
        changeCustomField(field) {
            this.action = field;
            this.send();
        },
        changeResponsible() {
            ElMessageBox.confirm(
                'Вы уверены? Вы можете потерять возможность редактировать задачу.',
                'Сменить ответственного',
                {
                    confirmButtonText: 'Хорошо',
                    cancelButtonText: 'Отмена',
                    type: 'warning',
                })
                .then(() => {
                    this.send()
                }).catch(() => {
                    this.thisTask.responsible = this.defaultResponsible;
                }
            );
        },
        loadMore (e) {
            if (this.loading) {
                return;
            }

            this.loading = true;

            let url = '/admin/task/' + this.thisTask.id + '/load_comments?';
            url += window.location.search;
            url += this.thisTask.comments?.length > 0 ? '&offset=' +  this.thisTask.comments.length : '';

            axios.get(url).then((response) => {
                this.thisTask.comments = this.thisTask.comments.concat(response.data.comments)
                this.loading = false;
                window.scrollTo(0, document.body.scrollHeight);
            });
        },
        sendComment(e) {
            this.visibleCommentForm = false;
            this.newComment.content = e.comment;
            if (this.newComment.content.length > 0) {
                this.thisTask.comments.unshift(this.newComment);
            }

            this.thisTask = this.taskPrepareDataByButtonOptions(e.button.action, this.thisTask);

            this.newComment = { id: '', type: 'comment', content: '', author_id: null, files: [] };
            this.send();
        },
        sendFiles(event) {
            this.visibleFileUploadForm = false;
            this.newComment.files = event;
            this.newComment.type = 'document';
            if (this.newComment.files?.length > 0) {
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
            this.thisTask = this.taskPrepareDataByButtonOptions(options, this.thisTask);
            this.send();
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
            let formData = this.taskFormData(this.thisTask, this.action, this.deleteCommentId)

            let url = '/admin/task/update';
            url += window.location.search

            axios
                .post(url,  formData)
                .then((response) => {
                    this.thisTask = response.data.task;
                    this.allStages = response.data.stages;
                    this.users = response.data.users;
                    this.stageButtons = response.data.task.stage.buttons;

                    this.permissionsUpdate();

                    this.action = null;
                    this.thisTask.all_fields = this.castFieldValue(this.thisTask.all_fields);
                    ElNotification({
                        title: 'Сохранено',
                        type: 'success',
                        position: 'bottom-right',
                    });
                }
            ).catch((failResponse) => {
                ElNotification({
                    duration: 8000,
                    title: 'Ошибка',
                    message: failResponse.response?.data?.message ?? '',
                    type: 'error',
                });
            });
        },
        permissionsUpdate() {
            this.permissions.can_change_members_self =  (this.auth.permission_names.find((item) => item === 'tasks.change_members_self')) !== undefined ? this.auth.id === this.thisTask.responsible_id : false;
        },
    }
}
</script>
