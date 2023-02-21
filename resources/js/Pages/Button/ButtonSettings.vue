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

    <el-button @click="openEditButton()">Добавить</el-button>


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



        Смена этапа на
        <el-select
            v-model="currentButton.options.pipeline_id"
            @change="selectActionPipeline"
            value-key="id"
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
        >
            <el-option
                v-for="stage in currentActionPipeline.stages"
                :key="stage.id"
                :label="stage.name"
                :value="stage.id"
            />
        </el-select>

        <el-button @click="save">Сохранить</el-button>
    </el-drawer>


</template>

<script>
import { ElMessageBox, ElSwitch } from 'element-plus'
export default {
    name: 'ButtonSettings',
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

            currentPipeline: this.pipelines[0],
            currentButton: {},

            currentActionPipeline: {},
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
            if (!!button) {
                this.allPipelines.forEach((pipeline) => {
                    if (pipeline.id === button.options.pipeline_id) {
                        this.currentActionPipeline = pipeline;
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

           this.currentButton = button;
           this.visibleDrawer = true;
        },
        save() {
            let data = {};
            data.id = this.currentButton.id ?? null;
            data.name = this.currentButton.name ?? null;
            data.pipeline_id = this.currentButton.pipeline_id ?? null;
            data.options = this.currentButton.options ?? null;

            console.log(data);

            axios.post('/admin/button/save', data)
                .then((response) => {
                    console.log(response)
                });


        }

    }
}
</script>

<style scoped>

</style>
