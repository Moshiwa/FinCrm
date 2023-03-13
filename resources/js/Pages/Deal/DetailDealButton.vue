<template>
    <a href="#" class="btn btn-primary" data-style="zoom-in" @click="openEditButton()">
        <span class="ladda-label">
            <i class="la la-plus"></i>
            Добавить Кнопку
        </span>
    </a>
    <div class="b-settings__container">
        <div class="b-settings__header">
            <div class="b-settings__pipelines">
                <div class="pipeline__buttons">
                    <div
                        v-for="pipeline in allPipelines" @click="selectPipeline(pipeline)"
                        :class="{
                            active: pipeline.id === this.currentPipeline.id,
                            'pipeline__item': true
                        }"
                    >
                        <a>{{ pipeline.name }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="b-settings__body">
            <div class="b-settings__buttons">
                <div
                    class="button__item"
                    v-for="button in currentPipeline.buttons"
                >
                    <action-button
                        :button="button"
                        @click="openEditButton(button)"
                    />
                </div>
            </div>

            <div
                v-if="this.currentPipeline.id"
                class="b-settings__actions"
            >
                <a :href="'/admin/pipeline/' + this.currentPipeline.id + '/edit'">Настроить воронку</a>
            </div>
            <div
                v-else
                class="b-settings__actions"
            >
                <a :href="'/admin/pipeline/create'">Создать воронку</a>
            </div>
        </div>
    </div>

    <el-drawer
        v-model="visibleDrawer"
        :show-close="false"
        title="Настройка кнопки"
        direction="rtl"
        size="50%"
    >
        <div class="popup__container">
            <div class="popup__content">
                <div class="popup__block">
                    <div class="popup__item">
                        <div class="item__title">Наименование</div>
                        <el-input
                            v-model="currentButton.name"
                        />
                    </div>
                    <div class="popup__item inline-flex">
                        <div class="flex-column mr-2">
                            <div class="item__title">Цвет</div>
                            <div class="color__container">
                            <div
                                v-for="color in colors"
                                class="color-item"
                                @click="currentColor = color"
                                :class="'btn-custom__' + color + ' ' + (currentColor === color ? 'active' : '')"
                            >
                            </div>
                        </div>
                        </div>
                        <div class="flex-column">
                            <div class="item__title">Иконка</div>
                            <div class="icon__container">
                            <div
                                v-for="icon in icons"
                                class="icon-item"
                                @click="currentIcon = icon"
                                :class="'las la-' + icon + ' ' + (currentIcon === icon ? 'active' : '')"
                            >
                            </div>
                        </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="popup__block">
                    <div class="popup__item">
                        <div class="item__title">Показать в</div>
                        <div v-for="stage in currentButton.visible">
                            <el-checkbox
                                v-model="stage.is_active"
                                :label="stage.name"
                            />
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="popup__block">
                    <div class="block__title">Действия</div>
                    <div class="popup__item">
                        <div class="item__title">Смена стадии</div>
                        <el-checkbox
                            v-model="actionChangeStage"
                            label="Менять"
                        />
                        <div v-if="actionChangeStage">
                            Смена этапа на

                            <el-select
                                v-model="currentButton.action.pipeline_id"
                                @change="selectActionPipeline"
                                value-key="id"
                                clearable
                            >
                                <el-option
                                    v-for="pipeline in allPipelines"
                                    :key="pipeline.id"
                                    :label="pipeline.name"
                                    :value="pipeline.id"
                                />
                            </el-select>
                            >
                            <el-select
                                v-model="currentButton.action.stage_id"
                                clearable
                            >
                                <el-option
                                    v-for="stage in currentActionPipeline.stages"
                                    :key="stage.id"
                                    :label="stage.name"
                                    :value="stage.id"
                                />
                            </el-select>
                        </div>
                    </div>
                    <div class="popup__item">
                        <div class="item__title">Смена ответственного</div>
                        <el-checkbox
                            v-model="actionChangeResponsible"
                            label="Менять"
                        />
                        <el-select
                            v-model="currentResponsible"
                            v-if="actionChangeResponsible"
                            value-key="id"
                            filterable
                            remote
                            reserve-keyword
                            placeholder="Please enter a keyword"
                            :remote-method="getUsers"
                        >
                            <el-option
                                v-for="user in responsibles"
                                :key="user.id"
                                :label="user.name"
                                :value="user"
                            />
                        </el-select>
                    </div>
                    <div class="popup__item">
                        <div class="item__title">Комментарий</div>
                        <el-checkbox
                            v-model="actionLeaveComment"
                            label="Оставить комментарий"
                        />
                    </div>
                    <hr>
                </div>
            </div>
            <div class="popup__actions">
                <el-button type="success" @click="save">Сохранить</el-button>
                <el-button type="danger" @click="remove">Удалить</el-button>
            </div>
        </div>
    </el-drawer>


</template>

<script>
export default {
    name: 'DetailDealButton',
    props: {
        pipelines: {
            type: Array,
            default: [],
        },
    },
    data() {
        return {
            visibleDrawer: false,

            allPipelines: this.pipelines ?? [],
            responsibles: [],

            currentResponsible: {},
            currentPipeline: this.pipelines[0] ?? { buttons: [] },
            currentButton: {},
            currentActionPipeline: {},
            currentColor: '',
            currentIcon: '',

            actionChangeStage: false,
            actionChangeResponsible: false,
            actionLeaveComment: false,

            colors: [
                'default', 'grey', 'black',
                'green', 'yellow', 'pink',
                'blue', 'red', 'purple', 'orange'
            ],
            icons: [
                'angle-double-right', 'comment', 'home',
                'bell', 'eye', 'pen', 'edit', 'undo', 'redo',
                'phone', 'user', 'trash', 'burn'
            ],

        }
    },
    mounted() {
        console.log(this.pipelines)
    },
    methods: {
        selectPipeline(pipeline) {
            this.currentPipeline = pipeline;
        },
        selectActionPipeline(pipeline_id) {
            if (!pipeline_id) {
                this.currentActionPipeline = {};
            }

            this.allPipelines.forEach((pipeline) => {
                if (pipeline.id === pipeline_id) {
                    this.currentActionPipeline = pipeline;
                    this.currentButton.action.stage_id = pipeline.stages[0].id;
                }
            })

        },
        openEditButton(button = null) {
            this.currentResponsible = {};
            this.currentActionPipeline = {};

            this.actionChangeStage = false;
            this.actionChangeResponsible = false;
            this.actionLeaveComment = false;
            if (!!button) {
                this.allPipelines.forEach((pipeline) => {
                    if (pipeline.id === button.pipeline_id) {
                        this.currentActionPipeline = this.findStagesForPipeline(button.action?.pipeline?.id);
                        this.currentResponsible = button.action.responsible ?? {};

                        this.responsibles = [this.currentResponsible];

                        this.actionChangeStage = !!button.action.stage_id;
                        this.actionChangeResponsible = !!button.action.responsible_id;
                        this.actionLeaveComment = !!button.action.comment;
                    }
                })
            } else {
                button = {
                    name: '',
                    visible: this.currentPipeline.stages,
                    color: 'default',
                    icon: 'angle-double-right',
                    action: {
                        pipeline: this.currentActionPipeline,
                    },
                    pipeline_id: this.currentPipeline.id
                }
            }
            this.currentColor = button.color
            this.currentIcon = button.icon
            this.currentButton = button;
            this.visibleDrawer = true;
        },
        save() {
            this.prepareData();
            let data = {};
            data.id = this.currentButton.id ?? null;
            data.name = this.currentButton.name ?? null;
            data.pipeline_id = this.currentButton.pipeline_id ?? null;
            data.action = this.currentButton.action ?? null;
            data.visible = this.currentButton.visible ?? null;
            data.color = this.currentColor ?? 'default';
            data.icon = this.currentIcon ?? 'angle-double-right';

            axios.post('/admin/deal/button/save', data)
                .then((response) => {
                    this.currentPipeline = response.data.data.pipeline ?? [];
                    this.visibleDrawer = false;
                });
        },
        remove() {
            axios.delete('/admin/deal/button/' + this.currentButton.id)
                .then((response) => {
                    this.currentPipeline.buttons.forEach((button, index) => {
                        if(button.id === this.currentButton.id) {
                            this.visibleDrawer = false;
                            this.currentPipeline.buttons.splice(index, 1);
                        }
                    })
                });
        },
        getUsers(query) {
            if (query.length >= 3) {
                axios.get('/admin/user/find-users?user_name=' + query)
                    .then((response) => {
                        this.responsibles = response.data.data;
                    });
            }
        },
        prepareData() {
            this.currentButton.action.stage_id = this.actionChangeStage ? this.currentButton.action.stage_id : '';
            this.currentButton.action.pipeline_id = this.actionChangeStage ? this.currentButton.action.pipeline_id : '';
            this.currentButton.action.responsible_id = this.actionChangeResponsible ? this.currentResponsible.id : '';
            this.currentButton.action.comment = !!this.actionLeaveComment;
        },
        findStagesForPipeline(pipeline_id) {
            let result = {};
            this.allPipelines.forEach((pipeline) => {
                if (pipeline.id === pipeline_id) {
                    result = pipeline
                }
            });

            return result;
        }

    }
}
</script>

<style scoped>
.b-settings__container {
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: 4px;
    margin-top: 10px;
    padding: 15px;
}
.b-settings__header {
    padding: 10px 0 10px 0;
}
.b-settings__pipelines {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-between;
    background: #f1f4f8;
    border-radius: 2px;
}
.pipeline__item {
    cursor: pointer;
    padding: 10px;
    background: #f1f4f8;
    color: #495057;
}
.pipeline__item:hover {
    color: var(--indigo);
    border-radius: 2px;
    filter: brightness(97%);
}
.pipeline__item.active {
    color: var(--indigo);
    border-radius: 2px;
    filter: brightness(97%);
}
.pipeline__buttons {
    display: flex;
    flex-direction: row;
}

.b-settings__body {
    display: flex;
    flex-direction: column;
    padding: 10px 0 10px 0;
}
.b-settings__buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.b-settings__actions {
    display: flex;
    flex-direction: row;
    justify-content: end;
    align-items: baseline;
    gap: 10px;
}


.popup__container {
    display: flex;
    flex-direction: column;
}
.popup__content {
    display: flex;
    flex-direction: column;
}
.color__container {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    width: 175px;
    gap: 5px;
}
.color-item {
    width: 30px;
    height: 30px;
    outline: 1px solid #dddddd;
    cursor: pointer;
    border-radius: 2px;
}
.color-item:hover {
    filter: brightness(95%);
}
.color-item.active {
    outline: 3px solid #dddddd;
}

.icon__container {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    width: 175px;
    gap: 5px;
}
.icon-item {
    width: 30px;
    height: 30px;
    outline: 1px solid #dddddd;
    cursor: pointer;
    border-radius: 2px;
    padding: 7px;
    background: white;
}
.icon-item:hover {
    filter: brightness(95%);
}
.icon-item.active {
    outline: 3px solid #dddddd;
}

.popup__block {
    display: flex;
    flex-direction: column;
}
.popup__actions {
    display: flex;
    flex-direction: row;
    justify-content: end;
}

.inline-flex {
    display: inline-flex;
}
</style>
