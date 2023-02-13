<template>
    <div class="entity-container">
        <div class="entity-items-container">
            <div v-for="pipeline in allPipelines">
                <el-button-group>
                    <el-button
                        @click="getPipelineInfo(pipeline)"
                        type="primary"
                    >
                        {{ pipeline.name }}
                    </el-button>
                    <el-button
                        @click="deletePipeline(pipeline)"
                        type="danger"
                    >
                        <el-icon class="el-icon--right">
                            <Delete />
                        </el-icon>
                    </el-button>
                </el-button-group>
        </div>
        </div>
        <el-button
            @click="createNewPipeline"
            type="primary"
        >
            Новая воронка
        </el-button>
    </div>
    <hr>

    <div class="settings-container">
        <el-form label-position="left" label-width="200px">
            <el-form-item label="Наименование воронки" prop="pipeline.name">
                <el-input
                    v-model="selectedPipeline.name"
                    class="w-50"
                    type="text"
                    autocomplete="off"
                />
            </el-form-item>

            <el-form-item label="Этапы воронки">
                <div class="stages-container w-50">

                    <div class="stage-item-container" v-for="stage in selectedPipeline.stages">
                        <el-input
                            class="w-50"
                            v-model="stage.name"
                        />
                        <el-color-picker v-model="stage.color" />
                        <el-button
                            @click="deleteStage(stage)"
                            type="danger"
                        >
                            <el-icon class="el-icon--right">
                                <Delete />
                            </el-icon>
                        </el-button>
                    </div>
                    <el-button
                        class="w-100"
                        type="primary"
                        @click="createNewStage"
                    >
                        Добавить
                    </el-button>
                </div>
            </el-form-item>
        </el-form>
        <el-button
            type="primary"
            @click="update"
        >
            Сохранить
        </el-button>
    </div>
</template>

<script>
import StageItem from './Components/StageItem.vue'
import { ElNotification } from 'element-plus'
export default {
    name: 'SettingsDeal',
    components: { StageItem },
    props: {
        pipelines: {
            type: Array
        },
    },
    data() {
        return {
            allPipelines: this.pipelines ?? [],
            selectedPipeline: this.pipelines[0] ?? {},
            newStage: { name: '', color: '' }
        }
    },
    methods: {
        update() {
            console.log(this.selectedPipeline);
            axios.post('/pipeline/update', this.selectedPipeline).then((response) => {

            });
        },
        createNewPipeline() {
            axios.post('/pipeline', { name: 'Новая' }).then((response) => {
                if (response.data.success === true) {
                    this.selectedPipeline = response.data.data;
                    this.allPipelines.push(this.selectedPipeline);
                }
            });
        },
        deletePipeline(pipeline) {
            axios.delete('/pipeline/' + pipeline.id).then((response) => {
                if (response.data.success === false) {
                    ElNotification({
                        title: 'Не удалось удалить сделку',
                        message: response.data.message,
                        type: 'error',
                        position: 'bottom-right',
                    });
                } else {
                    this.allPipelines.forEach((item, index) => {
                        if (item.id === pipeline.id) {
                            this.allPipelines.splice(index, 1);
                        }
                    });

                    this.selectedPipeline = this.allPipelines[this.allPipelines.length - 1] ?? {};
                }
            });
        },
        getPipelineInfo(pipeline) {
            this.selectedPipeline = pipeline;
        },
        createNewStage() {
            this.selectedPipeline.stages.push(this.newStage);
        },
        deleteStage() {
            console.log('delte')
        }
    }
}
</script>

<style scoped>
.entity-container {
    display: inline-flex;
    width: 100%;
    justify-content: space-between;
}
.entity-items-container {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 10px;
}
.settings-container {
    margin-top: 25px;
}
.stages-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.stage-item-container {
    display: inline-flex;
    gap: 10px;
}
</style>
