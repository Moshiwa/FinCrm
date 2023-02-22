<template>
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
                    <custom-button
                        :button="button"
                        @click="openEditButton(button)"
                    />
                </div>
            </div>

            <div class="b-settings__actions">
                <a :href="'/admin/pipeline/' + this.currentPipeline.id + '/edit'">Настройки воронки</a>
                <el-button type="primary"  @click="openEditButton()">Добавить</el-button>
            </div>
        </div>
    </div>




    <el-drawer
        v-model="visibleDrawer"
        :show-close="false"
        title="I have a nested table inside!"
        direction="rtl"
        size="50%"
    >
        <el-form-item
            label="Наименование"
        >
            <el-input
                v-model="currentButton.name"
            />
        </el-form-item>

        <el-form-item
            label="Показывать в"
        >
            <div v-for="stage in currentButton.visible">
                <el-checkbox
                    v-model="stage.is_active"
                    :label="stage.name"
                />
            </div>
        </el-form-item>

        Действия


        <el-checkbox
            v-model="actionChangeStage"
            label="Смена стадии"
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
                value-key="id"
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


        <el-checkbox
            v-model="actionChangeResponsible"
            label="Смена ответственного"
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

        <el-checkbox
            v-model="actionLeaveComment"
            label="Оставить комментарий"
        />

        <el-button type="success" @click="save">Сохранить</el-button>
        <el-button type="danger" @click="remove">Удалить</el-button>
    </el-drawer>


</template>

<script>
import { ElMessageBox, ElSwitch } from 'element-plus'
import CustomButton from "../../Components/CustomButton.vue";
export default {
    name: 'ButtonSettings',
    components: {
        CustomButton
    },
    props: {
        buttons: {
            type: Array,
            default: [],
        },
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
            currentPipeline: this.pipelines[0],
            currentButton: {},
            currentActionPipeline: {},

            actionChangeStage: false,
            actionChangeResponsible: false,
            actionLeaveComment: false,

            activePipelineClass: false
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
            this.allPipelines.forEach((pipeline) => {
                if (pipeline.id === pipeline_id) {
                    this.currentActionPipeline = pipeline;
                    this.currentButton.action.stage_id = pipeline.stages[0].id;
                }
            })

        },
        openEditButton(button = null) {
            this.changeStage = false;
            this.actionChangeResponsible = false;
            this.actionLeaveComment = false;
            if (!!button) {
                this.allPipelines.forEach((pipeline) => {
                    if (pipeline.id === button.pipeline_id) {
                        this.currentActionPipeline = pipeline;

                        this.actionChangeStage = !!button.action.stage_id;
                        this.actionChangeResponsible = !!button.action.responsible_id;
                        this.actionLeaveComment = !!button.action.comment;
                    }
                })
            } else {
                let stages = this.currentPipeline.stages;
                button = {
                    name: '',
                    visible: stages,
                    action: {},
                    pipeline_id: this.currentPipeline.id
                }
            }
            console.log(button);
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

            axios.post('/admin/button/save', data)
                .then((response) => {
                    this.currentPipeline = response.data.data.pipeline ?? [];
                    this.visibleDrawer = false;
                });
        },
        remove() {
            axios.delete('/admin/button/' + this.currentButton.id)
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




</style>
