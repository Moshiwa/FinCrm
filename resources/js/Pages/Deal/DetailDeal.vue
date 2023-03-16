<template>
    <div class="row">
        <div class="card row common-gap">
            <div class="card-left">
                <el-divider content-position="center">СДЕЛКА</el-divider>
                <el-input
                    class="input-title hidden-border"
                    v-model="thisDeal.name"
                    @change="send"
                />
                <el-form-item label="Воронка/Стадия" class="select-container">
                    <el-select
                        v-model="thisDeal.pipeline"
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
                        v-model="thisDeal.stage"
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
                        v-model="thisDeal.responsible"
                        value-key="id"
                        filterable
                        remote
                        reserve-keyword
                        placeholder="Please enter a keyword"
                        @change="send"
                    >
                        <el-option
                            v-for="user in responsibles"
                            :key="user.id"
                            :label="user.name"
                            :value="user"
                        />
                    </el-select>
                </el-form-item>

                <el-divider content-position="left">Дополнительные поля</el-divider>
                <el-form-item
                    v-for="field in thisDeal.all_fields"
                    :label="field.name"
                    :required="field.is_required"
                >
                    <field
                        :field="field"
                        @send="send"
                    />
                </el-form-item>
                <div>
                    <a href="/admin/field/create?entity=deal">Добавить поле</a>
                </div>

                <el-divider content-position="center">КЛИЕНТ</el-divider>

                <el-input
                    class="input-subtitle hidden-border"
                    v-model="thisDeal.client.name"
                    @change="send"
                />
                <el-divider content-position="left">Дополнительные поля</el-divider>
                <el-form-item
                    v-for="field in thisDeal.client.all_fields"
                    :label="field.name"
                    :required="field.is_required"
                >
                    <field
                        :field="field"
                        :is-sender-prefix="true"
                        @send="send"
                    />
                </el-form-item>
                <div>
                    <a href="/admin/field/create?entity=client">Добавить поле</a>
                </div>
            </div>

            <div class="card-right">
                <filters
                    :filter="filter"
                />
                <div class="card-body row">
                    <el-timeline class="infinite-list">
                        <comments
                            :comments="thisDeal.comments"
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
            :stage="thisDeal.stage"
            :is-delete="permissions.can_delete"
            :is-upload="true"
            @commentSend="sendComment($event)"
            @filesSend="sendFiles($event)"
            @changeData="changeData($event)"
            @deleteAction="deleteDeal"
        />

    </div>
</template>

<script>
import {ElInput, ElMessageBox} from 'element-plus';
import Contenteditable from "../../Components/Contenteditable.vue";
import FileUpload from "../../Components/FileUpload.vue";
import { ElNotification } from 'element-plus'
import ActionButtons from "../../Components/ActionButtons.vue";
import Comments from "../../Components/Comments.vue";
import Field from "../../Components/Field.vue";
import Helper from "../../Mixins/Helper.vue";
import Filters from "../../Components/Filters.vue";

export default {
    name: 'DetailDeal',
    components: {
        Contenteditable,
        FileUpload,
        ActionButtons,
        Field,
        Comments,
        Filters
    },
    mixins: [
        Helper
    ],
    props: {
        auth: {
            type: Object,
            required: true
        },
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

            thisDeal: this.deal,
            allPipelines: this.pipelines ?? [],
            allStages: this.stages ?? [],
            stageButtons: this.buttons ?? [],
            responsibles: this.users ?? [],

            deleteCommentId: 0,
            action: {},

            newComment: { id: '', type: 'comment', content: '', author_id: null, files: [] },

            permissions: {
                can_delete: this.auth.permission_names.find((item) => item === 'deals.delete'),
            }
        }
    },
    beforeMount() {
        console.log(this.thisDeal);
        $(document).on('scroll', this.loadMore);
        this.thisDeal.all_fields = this.castFieldValue(this.thisDeal.all_fields);
        this.thisDeal.client.all_fields = this.castFieldValue(this.thisDeal.client.all_fields);
    },
    methods: {
        changePipeline(item) {
            axios
                .get('/admin/deal/get_stages/' + item.id,)
                .then((response) => {
                    this.allStages = response.data;
                    this.thisDeal.stage = response.data[0] ?? {}
                    this.send();
                });
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

                let url = '/admin/deal/' + this.thisDeal.id + '/load_comments?';
                url += window.location.search
                if (this.thisDeal.comments?.length > 0) {
                    url+= '&offset=' +  this.thisDeal.comments.length;
                }

                axios.get(url).then((response) => {
                    this.thisDeal.comments = this.thisDeal.comments.concat(response.data.comments)
                    this.loading = false;
                })
            }
        },
        sendComment(e) {
            this.visibleCommentForm = false;
            this.newComment.content = e.comment;
            if (this.newComment.content.length > 0) {
                this.thisDeal.comments.unshift(this.newComment);
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
                this.thisDeal.comments.unshift(this.newComment);
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
        prepareDataByButtonOptions(action) {
            this.action = action;
            this.thisDeal.pipeline.id = !!action.pipeline_id ? action.pipeline_id : this.thisDeal.pipeline.id;
            this.thisDeal.stage.id = !!action.stage_id ? action.stage_id : this.thisDeal.stage.id;
            this.thisDeal.responsible.id = !!action.responsible_id ? action.responsible_id : this.thisDeal.responsible.id;
        },
        deleteDeal() {
            ElMessageBox.confirm(
                'Вы уверены?',
                'Удалить сделку',
                {
                    confirmButtonText: 'Хорошо',
                    cancelButtonText: 'Отмена',
                    type: 'warning',
                }
            )
                .then(() => {
                    axios
                        .delete('/admin/deal/' + this.thisDeal.id)
                        .then((response) => {
                            location.href = '/admin/deal/';
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
            formData.append('id', this.thisDeal.id);
            formData.append('name', this.thisDeal.name);
            formData.append('pipeline_id', this.thisDeal.pipeline.id);
            formData.append('stage_id', this.thisDeal.stage.id);
            formData.append('responsible_id', this.thisDeal.responsible.id);
            formData.append('client_id', this.thisDeal.client_id);

            formData.append('comment_count', this.thisDeal.comments.length ?? 0);

            if (!!this.deleteCommentId) {
                formData.append('delete_comment_id', this.deleteCommentId);
            }

            this.thisDeal.all_fields = this.thisDeal.all_fields ?? [];
            this.thisDeal?.all_fields.forEach((field, fieldIndex) => {
                formData.append('fields[' + field.id + '][value]', field.pivot?.value ?? '');
            });

            this.thisDeal.client = this.thisDeal.client ?? [];
            formData.append('client[name]', this.thisDeal.client?.name);
            this.thisDeal.client.all_fields = this.thisDeal.client?.all_fields ?? [];
            this.thisDeal.client?.all_fields.forEach((field, fieldIndex) => {
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

            let url = '/admin/deal/update';
            url += window.location.search

            axios
                .post(url,  formData)
                .then((response) => {
                    this.thisDeal = response.data.deal;
                    this.allStages = response.data.stages;
                    this.allPipelines = response.data.pipelines;
                    this.responsibles = response.data.users;
                    this.stageButtons = response.data.deal.pipeline.buttons;
                    this.action = null;

                    this.thisDeal.fields = this.castFieldValue(this.thisDeal.fields);
                    this.thisDeal.client.fields = this.castFieldValue(this.thisDeal.client.fields);

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
