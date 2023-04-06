<template>
    <a
        v-if="permissions.can_create"
        href="#"
        class="btn btn-primary"
        data-style="zoom-in"
        @click="openEditButton()"
    >
        <span class="ladda-label">
            <i class="la la-plus"></i>
            Добавить Кнопку
        </span>
    </a>
    <div class="b-settings__container">
        <div class="b-settings__header">
        </div>
        <div class="b-settings__body">
            <div class="b-settings__buttons">
                <div
                    class="button__item"
                    v-for="button in allButtons"
                >
                    <action-button
                        :button="button"
                        @click="openEditButton(button)"
                    />
                </div>
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
                        <div class="flex-column mr-2" style="width: auto">
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
                        <div class="flex-column" style="width: auto">
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
                        <div class="item__title">Смена дедлайна</div>
                        <el-checkbox
                            v-model="actionChangeDeadline"
                            label="Менять"
                        />
                        <div v-if="actionChangeDeadline">
                            Смена дедлайна на

                            <el-input-number
                                v-model="currentButton.action.deadline_value"
                            />
                            <el-select
                                v-model="currentButton.action.deadline_format_id"
                                value-key="id"
                                clearable
                            >
                                <el-option
                                    v-for="format in deadlineFormats"
                                    :key="format.id"
                                    :label="format.name"
                                    :value="format.id"
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
                            v-model="currentButton.action.responsible_id"
                            v-if="actionChangeResponsible"
                            value-key="id"
                            filterable
                            remote
                            reserve-keyword
                            placeholder="Выберите ответственного"
                        >
                            <el-option
                                v-for="user in allUsers"
                                :key="user.id"
                                :label="user.name"
                                :value="user.id"
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
                            v-model="currentButton.action.manager_id"
                            v-if="actionChangeManager"
                            value-key="id"
                            filterable
                            remote
                            reserve-keyword
                            placeholder="Выберите наблюдателя"
                        >
                            <el-option
                                v-for="user in allUsers"
                                :key="user.id"
                                :label="user.name"
                                :value="user.id"
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
                            v-model="currentButton.action.executor_id"
                            v-if="actionChangeExecutor"
                            value-key="id"
                            filterable
                            remote
                            reserve-keyword
                            placeholder="Выберите исполнителя"
                        >
                            <el-option
                                v-for="user in allUsers"
                                :key="user.id"
                                :label="user.name"
                                :value="user.id"
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
                <el-button
                    v-if="permissions.can_update"
                    type="success"
                    @click="save"
                >
                    Сохранить
                </el-button>
                <el-button
                    v-if="permissions.can_delete"
                    type="danger"
                    @click="remove"
                >
                    Удалить
                </el-button>
            </div>
        </div>
    </el-drawer>
</template>

<script>

import {ElNotification} from "element-plus";

export default {
    name: 'DetailTaskButton',
    props: {
        auth: {
            type: Object,
            default: {}
        },
        buttons: {
            type: Array,
            default: [],
        },
        taskStages: {
            type: Array,
            default: [],
        },
        users: {
            type: Array,
            default: []
        },
        deadlineFormats: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            visibleDrawer: false,

            allTaskStages: this.taskStages ?? [],
            allUsers: this.users ?? [],
            allButtons: this.buttons ?? [],

            currentButton: {},
            currentColor: '',
            currentIcon: '',

            actionChangeStage: false,
            actionChangeResponsible: false,
            actionChangeManager: false,
            actionChangeExecutor: false,
            actionLeaveComment: false,
            actionChangeDeadline: false,

            permissions: {
                can_delete: this.auth.permission_names.find((item) => item === 'task_buttons.delete'),
                can_update: this.auth.permission_names.find((item) => item === 'task_buttons.update') || this.auth.permission_names.find((item) => item === 'task_buttons.create'),
                can_create: this.auth.permission_names.find((item) => item === 'task_buttons.create'),
            },

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
    methods: {
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
            this.actionChangeDeadline = false;
            if (!!button) {
                this.actionLeaveComment = !!button.action.comment;
                this.actionChangeStage = !!button.action.task_stage_id;
                this.actionChangeResponsible = !!button.action.responsible_id;
                this.actionChangeManager = !!button.action.manager_id;
                this.actionChangeExecutor = !!button.action.executor_id;
                this.actionChangeDeadline = !!button.action.deadline_value && !!button.action.deadline_format_id;
            } else {
                button = {
                    name: '',
                    visible: this.allTaskStages,
                    color: 'default',
                    icon: 'angle-double-right',
                    action: {
                        stage: this.currentActionStage,
                    },
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
            data.action = this.currentButton.action ?? null;
            data.visible = this.currentButton.visible ?? null;
            data.color = this.currentColor ?? 'default';
            data.icon = this.currentIcon ?? 'angle-double-right';

            axios.post('/admin/task/button/save', data)
                .then((response) => {
                    this.allButtons = response.data.data.buttons ?? [];
                    this.allTaskStages = response.data.data.task_stages ?? [];
                    this.visibleDrawer = false;
                })
                .catch((response) => {
                    this.visibleDrawer = false;
                    let error = response.response.data.message ?? '';
                    ElNotification({
                        duration: 8000,
                        title: 'Ошибка',
                        message: error,
                        type: 'error',
                    });
                });
        },
        prepareData() {
            this.currentButton.action.task_stage_id = this.actionChangeStage ? this.currentButton.action.task_stage_id : '';
            this.currentButton.action.responsible_id = this.actionChangeResponsible ? this.currentButton.action.responsible_id : '';
            this.currentButton.action.manager_id = this.actionChangeManager ? this.currentButton.action.manager_id : '';
            this.currentButton.action.executor_id = this.actionChangeExecutor ? this.currentButton.action.executor_id : '';
            this.currentButton.action.deadline_value = this.actionChangeDeadline ? this.currentButton.action.deadline_value : '';
            this.currentButton.action.deadline_format_id = this.actionChangeDeadline ? this.currentButton.action.deadline_format_id : '';
            this.currentButton.action.comment = !!this.actionLeaveComment;
        },
        remove() {
            axios.delete('/admin/task/button/' + this.currentButton.id)
                .then((response) => {
                    this.allButtons.forEach((button, index) => {
                        if(button.id === this.currentButton.id) {
                            this.visibleDrawer = false;
                            this.allButtons.splice(index, 1);
                        }
                    })
                })
                .catch((response) => {
                    this.visibleDrawer = false;
                    let error = response.response.data.message ?? '';
                    ElNotification({
                        duration: 8000,
                        title: 'Ошибка',
                        message: error,
                        type: 'error',
                    });
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
