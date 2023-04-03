<template>
    <div class="row">
        <div class="card row common-gap">
            <div class="card-left">
                <el-input
                    class="input-title hidden-border"
                    :disabled="!permissions.can_update_deal"
                    v-model="thisDeal.name"
                    @change="send"
                />
                <el-divider content-position="center">СДЕЛКА</el-divider>
                <el-form-item label="Воронка/Стадия" class="select-container">
                    <el-select
                        v-model="thisDeal.pipeline"
                        :disabled="!(permissions.can_change_pipeline && permissions.can_update_deal)"
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
                        :disabled="!(permissions.can_change_stage && permissions.can_update_deal)"
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
                        :disabled="!(permissions.can_update_deal && (permissions.can_change_responsible || permissions.can_change_members_self))"
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
                        :disabled="!permissions.can_update_deal"
                        @send="changeCustomField(field)"
                    />
                </el-form-item>
                <div v-if="permissions.can_create_field">
                    <a href="/admin/field/create?entity=deal">Добавить поле</a>
                </div>

                <el-divider content-position="center">КЛИЕНТ</el-divider>

                <el-input
                    class="input-subtitle hidden-border"
                    v-model="thisDeal.client.name"
                    :disabled="!permissions.can_update_client"
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
                        :disabled="!permissions.can_update_client"
                        @send="changeCustomField(field)"
                        @sendMessage="sendRemoteMessage($event)"
                        @call="call($event)"
                    />
                </el-form-item>
                <div v-if="permissions.can_create_field">
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
import FileUpload from "../../Components/FileUpload.vue";
import { ElNotification } from 'element-plus'
import ActionButtons from "../../Components/ActionButtons.vue";
import Comments from "../../Components/Comments.vue";
import Field from "../../Components/Field.vue";
import Helper from "../../Mixins/Helper.vue";
import Filters from "../../Components/Filters.vue";
import DataGenerateHelper from "../../Mixins/DataGenerateHelper.vue";

export default {
    name: 'DetailDeal',
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
                can_change_pipeline: this.auth.permission_names.find((item) => item === 'deals.change_pipeline'),
                can_change_stage: this.auth.permission_names.find((item) => item === 'deals.change_stage'),
                can_change_responsible: this.auth.permission_names.find((item) => item === 'deals.change_responsible'),
                can_change_members_self: (this.auth.permission_names.find((item) => item === 'deals.change_members_self')) !== undefined ? this.auth.id === this.deal.responsible_id : false,
                can_create_field: this.auth.permission_names.find((item) => item === 'fields.create'),
                can_update_client: this.auth.permission_names.find((item) => item === 'clients.update'),
                can_update_deal: this.auth.permission_names.find((item) => item === 'deals.update'),
            }
        }
    },
    beforeMount() {
        this.thisDeal.all_fields = this.castFieldValue(this.thisDeal.all_fields);
        this.thisDeal.client.all_fields = this.castFieldValue(this.thisDeal.client.all_fields);
    },
    created() {
        console.log('Created')
        /*let channel = Echo.channel('record');
        channel.listen('WebhookCommentPush', (e) => {
            this.thisDeal.comments.unshift(e.comment);
        });*/
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
        changeCustomField(field) {
            this.action = field;
            this.send();
        },
        changeData(options) {
            this.thisDeal = this.dealPrepareDataByButtonOptions(options, this.thisDeal);
            this.send();
        },
        sendComment(e) {
            this.visibleCommentForm = false;
            this.newComment.content = e.comment;
            if (this.newComment.content.length > 0) {
                this.thisDeal.comments.unshift(this.newComment);
            }

            this.thisDeal = this.dealPrepareDataByButtonOptions(e.button.action, this.thisDeal);
            this.newComment = { id: '', type: 'comment', content: '', author_id: null, files: [] };
            this.send();
        },
        sendFiles(event) {
            this.visibleFileUploadForm = false;
            this.newComment.files = event;
            this.newComment.type = 'document';
            if (this.newComment.files?.length > 0) {
                this.thisDeal.comments.unshift(this.newComment);
            }

            this.newComment = { id: '', type: 'comment', content: '', author_id: null, files: [] };
            this.send();
        },
        call(event) {
            ElMessageBox.confirm(
                'Продолжить?',
                'Позвонить',
                {
                    confirmButtonText: 'Хорошо',
                    cancelButtonText: 'Отмена',
                    type: 'success',
                }
            )
                .then(() => {
                    axios.post('/admin/telephony/call', {
                        phone: event.pivot.value,
                        deal_id: this.thisDeal.id
                    });

                    //ToDo замена сокетов (каждые две секунды проверяем пришел ли вебхук)
                    setInterval(() => {
                        axios.get('/admin/telephony/check?user=' + this.auth.id + '&deal=' + this.thisDeal.id).then((response) => {
                            if (response.data.success === true) {
                                this.thisDeal.comments.unshift(response.data.data);
                            }
                        })
                    }, 2000)
                });
        },
        sendRemoteMessage(event) {
            axios.post('/admin/sender/send', {
                'integration': event.integration,
                'message': event.message,
                'recipient': event.field.pivot.value,
                'client_id': this.thisDeal.client.id,
                'deal_id': this.thisDeal.id
            }).then((response) => {
                if (!!response.data.success === true) {
                    ElNotification({
                        title: 'Доставлено',
                        message: response.data.message,
                        type: 'success',
                    });

                    this.thisDeal = response.data.data ?? [];
                } else {
                    ElNotification({
                        duration: 8000,
                        title: 'Ошибка',
                        message: response.data.message,
                        type: 'error',
                    })
                }
            }).catch((failResponse) => {
                if (!!failResponse.response.data.message) {
                    ElNotification({
                        duration: 8000,
                        title: 'Ошибка',
                        message: failResponse.response.data.message,
                        type: 'error',
                    })
                }
            });
        },
        prepareCommentDataSend(event) {
            if (event) {
                this.deleteCommentId = event;
            }
            this.send();
            this.deleteCommentId = null;
        },
        deleteDeal() {
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
        },
        send() {
            let formData = this.dealFormData(this.thisDeal, this.action, this.deleteCommentId)
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

                    this.permissionsUpdate();

                    this.thisDeal.all_fields = this.castFieldValue(this.thisDeal.fields);
                    this.thisDeal.client.all_fields = this.castFieldValue(this.thisDeal.client.fields);

                    ElNotification({
                        title: 'Сохранено',
                        type: 'success',
                        position: 'bottom-right',
                    });
                }
            )
        },
        loadMore (e) {
            if (!!this.loading) {
                return;
            }

            this.loading = true;
            let url = '/admin/deal/' + this.thisDeal.id + '/load_comments?';
            url += window.location.search;
            url += this.thisDeal.comments?.length > 0 ? '&offset=' +  this.thisDeal.comments.length : '';
            axios.get(url).then((response) => {
                this.thisDeal.comments = this.thisDeal.comments.concat(response.data.comments);
                this.loading = false;
                window.scrollTo(0, document.body.scrollHeight);
            });
        },
        permissionsUpdate() {
            this.permissions.can_change_members_self = (this.auth.permission_names.find((item) => item === 'deals.change_members_self')) !== undefined ? this.auth.id === this.thisDeal.responsible_id : false;
        },
    }
}
</script>
