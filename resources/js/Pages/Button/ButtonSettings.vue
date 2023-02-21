<template>
    <div>
        Выберите воронку:
        <div v-for="pipeline in allPipelines" @click="selectPipeline(pipeline)">
            <a style="cursor: pointer; text-decoration: underline">{{ pipeline.name }}</a>
        </div>
    </div>

    <div v-for="button in currentPipeline.buttons">
        <el-button plain @click="openEditButton(button)">{{ button.name }}</el-button>
    </div>

    <el-button type="primary"  @click="openEditButton()">Добавить</el-button>


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
            <div v-for="stage in currentButton.options.display.stages">
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
                v-model="currentButton.options.pipeline_id"
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
                v-model="currentButton.options.stage_id"
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
export default {
    name: 'ButtonSettings',
    components: {
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
                    this.currentButton.options.stage_id = pipeline.stages[0].id;
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

                        this.actionChangeStage = !!button.options.stage_id;
                        this.actionChangeResponsible = !!button.options.responsible_id;
                        this.actionLeaveComment = !!button.options.comment;
                    }
                })
            } else {
                let stages = this.currentPipeline.stages;
                button = {
                    name: '',
                    options: {
                        display: {
                            stages: stages
                        }
                    },
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
            data.options = this.currentButton.options ?? null;

            console.log(data);

            axios.post('/admin/button/save', data)
                .then((response) => {
                    console.log(response)
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
            this.currentButton.options.stage_id = this.actionChangeStage ? this.currentButton.options.stage_id : '';
            this.currentButton.options.pipeline_id = this.actionChangeStage ? this.currentButton.options.pipeline_id : '';
            this.currentButton.options.responsible_id = this.actionChangeResponsible ? this.currentResponsible.id : '';
            this.currentButton.options.comment = !!this.actionLeaveComment;
        }

    }
}
</script>

<style scoped>

</style>
