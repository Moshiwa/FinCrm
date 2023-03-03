<template>
    <a href="#" class="btn btn-primary" data-style="zoom-in" @click="openEditButton()">
        <span class="ladda-label">
            <i class="la la-plus"></i>
            Добавить Кнопку
        </span>
    </a>
    <div class="b-settings__container">
        <div class="b-settings__header">
            <div class="b-settings__stages">
                <div class="stage__buttons">
                    <div
                        v-for="taskStage in allTaskStages" @click="selectStage(taskStage)"
                        :class="{
                            active: taskStage.id === this.currentTaskStage.id,
                            'stage__item': true
                        }"
                    >
                        <a>{{ taskStage.name }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="b-settings__body">
            <div class="b-settings__buttons">
                <div
                    class="button__item"
                    v-for="button in currentTaskStage.buttons"
                >
                    <action-button
                        :button="button"
                        @click="openEditButton(button)"
                    />
                </div>
            </div>

            <div
                v-if="this.currentTaskStage.id"
                class="b-settings__actions"
            >
                <a :href="'/admin/task-stage/' + this.currentTaskStage.id + '/edit'">Настроить статус</a>
            </div>
            <div
                v-else
                class="b-settings__actions"
            >
                <a :href="'/admin/task-stage/create'">Создать статус</a>
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
                                v-model="currentButton.action.task_stage_id"
                                @change="selectActionStage"
                                value-key="id"
                                clearable
                            >
                                <el-option
                                    v-for="stage in allTaskStages"
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
                            v-model="currentButton.action.responsible"
                            v-if="actionChangeResponsible"
                            value-key="id"
                            filterable
                            remote
                            reserve-keyword
                            placeholder="Please enter a keyword"
                        >
                            <el-option
                                v-for="user in allUsers"
                                :key="user.id"
                                :label="user.name"
                                :value="user"
                            />
                        </el-select>
                    </div>
                    <div class="popup__item">
                        <div class="item__title">Смена наблюдателя</div>
                        <el-checkbox
                            v-model="actionChangeManager"
                            label="Менять"
                        />
                        <el-select
                            v-model="currentButton.action.manager"
                            v-if="actionChangeManager"
                            value-key="id"
                            filterable
                            remote
                            reserve-keyword
                            placeholder="Please enter a keyword"
                        >
                            <el-option
                                v-for="user in allUsers"
                                :key="user.id"
                                :label="user.name"
                                :value="user"
                            />
                        </el-select>
                    </div>
                    <div class="popup__item">
                        <div class="item__title">Смена исполнителя</div>
                        <el-checkbox
                            v-model="actionChangeExecutor"
                            label="Менять"
                        />
                        <el-select
                            v-model="currentButton.action.executor"
                            v-if="actionChangeExecutor"
                            value-key="id"
                            filterable
                            remote
                            reserve-keyword
                            placeholder="Please enter a keyword"
                        >
                            <el-option
                                v-for="user in allUsers"
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
    name: 'DetailTaskButton',
    props: {
        taskStages: {
            type: Array,
            default: [],
        },
        users: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            visibleDrawer: false,

            allTaskStages: this.taskStages ?? [],
            allUsers: this.users ?? [],

            currentTaskStage: this.taskStages[0] ?? { buttons: [] },
            currentButton: {},
            currentColor: '',
            currentIcon: '',

            actionChangeStage: false,
            actionChangeResponsible: false,
            actionChangeManager: false,
            actionChangeExecutor: false,
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
        console.log(this.taskStages)
    },
    methods: {
        selectStage(stage) {
            console.log(stage)
            this.currentTaskStage = stage;
        },
        selectActionStage(stage_id) {
            if (!stage_id) {
                this.currentActionStage = {};
            }

            this.allTaskStages.forEach((stage) => {
                if (stage.id === stage_id) {
                    this.currentActionStage = stage;
                }
            })

        },
        openEditButton(button = null) {
            this.actionChangeStage = false;
            this.actionChangeResponsible = false;
            this.actionChangeManager = false;
            this.actionChangeExecutor = false;
            this.actionLeaveComment = false;
            if (!!button) {
                this.allTaskStages.forEach((stage) => {
                    if (stage.id === button.task_stage_id) {
                        this.actionLeaveComment = !!button.action.comment;
                        this.actionChangeStage = !!button.action.task_stage_id;
                        this.actionChangeResponsible = !!button.action.responsible_id;
                        this.actionChangeManager = !!button.action.manager_id;
                        this.actionChangeExecutor = !!button.action.executor_id;
                    }
                })
            } else {
                button = {
                    name: '',
                    visible: this.allTaskStages,
                    color: 'default',
                    icon: 'angle-double-right',
                    action: {
                        stage: this.currentActionStage,
                    },
                    task_stage_id: this.currentTaskStage.id
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
            console.log(this.currentButton);
            data.id = this.currentButton.id ?? null;
            data.name = this.currentButton.name ?? null;
            data.task_stage_id = this.currentButton.task_stage_id ?? null;
            data.action = this.currentButton.action ?? null;
            data.visible = this.currentButton.visible ?? null;
            data.color = this.currentColor ?? 'default';
            data.icon = this.currentIcon ?? 'angle-double-right';

            axios.post('/admin/task/buttons/save', data)
                .then((response) => {
                    this.currentTaskStage = response.data.data.task_stage ?? [];
                    this.visibleDrawer = false;
                });
        },
        prepareData() {
            this.currentButton.action.task_stage_id = this.actionChangeStage ? this.currentButton.action.task_stage_id : '';
            this.currentButton.action.responsible_id = this.actionChangeResponsible ? this.currentButton.action.responsible.id : '';
            this.currentButton.action.manager_id = this.actionChangeManager ? this.currentButton.action.manager.id : '';
            this.currentButton.action.executor_id = this.actionChangeExecutor ? this.currentButton.action.executor.id : '';
         /*   this.currentButton.action.start_time = this.a ? this.currentButton.action.executor_id : '';
            this.currentButton.action.end_time = this.actionChangeExecutor ? this.currentButton.action.executor_id : '';*/
            this.currentButton.action.comment = !!this.actionLeaveComment;
            console.log( this.actionChangeExecutor );
        },
        remove() {
            axios.delete('/admin/task/buttons/' + this.currentButton.id)
                .then((response) => {
                    this.currentTaskStage.buttons.forEach((button, index) => {
                        if(button.id === this.currentButton.id) {
                            this.visibleDrawer = false;
                            this.currentTaskStage.buttons.splice(index, 1);
                        }
                    })
                });
        },
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
.b-settings__stages {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-between;
    background: #f1f4f8;
    border-radius: 2px;
}
.stage__item {
    cursor: pointer;
    padding: 10px;
    background: #f1f4f8;
    color: #495057;
}
.stage__item:hover {
    color: var(--indigo);
    border-radius: 2px;
    filter: brightness(97%);
}
.stage__item.active {
    color: var(--indigo);
    border-radius: 2px;
    filter: brightness(97%);
}
.stage__buttons {
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
