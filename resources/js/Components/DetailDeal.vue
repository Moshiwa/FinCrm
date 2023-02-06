<template>
    <div class="wrap">
        <div class="col-md-5 bold-labels">
            <div class="card">
                <div class="card-body row">
                    <el-row class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <el-select
                            v-model="pipeline.id"
                            size="small"
                            @change="changePipeline(pipeline)"
                        >
                            <el-option
                                v-for="item in pipelines"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            />
                        </el-select>
                        <el-select
                            v-model="stage.id"
                            size="small"
                            @change="changeStage(stage)"
                        >
                            <el-option
                                v-for="item in stages"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            />
                        </el-select>
                        <hr>
                    </el-row>
                    <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <div class="row-label">Наименование</div>
                        <contenteditable
                            v-model="deal.name"
                            @send="send"
                        />
                    </el-row>
                    <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <div class="row-label">Комментарий</div>
                        <contenteditable
                            v-model="deal.comment"
                            @send="send"
                        />
                    </el-row>
                    <hr>
                    <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <div class="row-label">Имя</div>
                        <contenteditable
                            v-model="client.name"
                            @send="send"
                        />
                    </el-row>
                    <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <div class="row-label">Телефон</div>
                        <contenteditable
                            v-model="client.phone"
                            @send="send"
                        />
                    </el-row>
                </div>
            </div>
        </div>

        <div class="col-md-4 bold-labels">
            <div class="card">
                <div class="card-body row">

                    <el-row
                        :gutter="20"
                        class="ml-3 w-35 text-gray-600 inline-flex data-row"
                        v-for="comment in comments"
                    >
                        <contenteditable
                            v-model="comment.content"
                            @send="send"
                        />
                        <div @click="deleteComment(comment)">Удалить</div>
                    </el-row>
                </div>
            </div>
        </div>

        <div class="col-md-2 bold-labels">
            <div class="card">
                <div class="card-body row">
                    <span>Смена воронки</span>
                    <el-select
                        v-model="deal.pipeline"
                        size="small"
                        @change="changePipeline"
                    >
                        <el-option
                            v-for="item in pipelines"
                            :key="item.id"
                            :label="item.name"
                            :value="item"
                        />
                    </el-select>
                    <hr>
                </div>
                <div class="card-body row">
                    <span>Смена стадии</span>
                    <el-select v-model="deal.stage" size="small">

                    </el-select>
                    <hr>
                </div>
                <div class="card-body row">
                    <span>Кнопки</span>
                    <el-button @click="visibleCommentForm = true">Оставить комментарий</el-button>
                    <hr>
                </div>
            </div>
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

</template>

<script>

import { ElInput } from 'element-plus';

export default {
    name: 'DetailDeal',
    components: [
    ],
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
            client: this.deal?.client ?? {},
            pipeline: this.deal?.pipeline ?? {},
            responsible: this.deal?.responsible ?? {},
            stage: this.deal?.stage ?? {},
            stages: this.stages ?? [],
            comments: this.deal.comments ?? [],
            newComment: { type: 'text', content: '', author_id: null },
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
        sendComment() {
            this.visibleCommentForm = false;
            this.send();
            this.newComment = { type: 'text', content: '', author_id: null };
        },
        deleteComment(comment) {
            this.comments.forEach((item, index) => {
                if (item.id === comment.id) {
                    this.comments.splice(index, 1)
                }
            })

            this.send();
        },
        send() {
            this.deal.new = {};

            this.deal.client = this.client;
            this.deal.pipeline_id = this.pipeline.id;
            this.deal.responsible = this.responsible;
            this.deal.stage_id = this.stage.id;
            this.deal.comments = this.comments;

            if (this.newComment.content.length > 0) {
                this.deal.comments.push(this.newComment);
            }

            console.log( this.deal)
            axios.post('/deal/update',  this.deal)
        },
        definitionCommentType(text) {
            switch (text) {
                case 'text':
                    return 'Комментарий'
                case 'audio':
                    return 'Аудиозапись'
                case 'video':
                    return 'Видеозапись'
                case 'doc':
                    return 'Документ'
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

.bold-labels {
    padding-left: 0px;
}
.row-label {
    min-width: 125px;
}
.data-row {
    display: flex;
    width: 100%;
    gap: 10px
}
</style>
