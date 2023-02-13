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
            newStage: { name: '', color: '' },
            deletedStages: [],
        }
    },
    methods: {
        update() {
            const formData = new FormData();
            formData.append('id', this.selectedPipeline.id);
            formData.append('name', this.selectedPipeline.name);
            this.selectedPipeline.stages = this.selectedPipeline.stages ?? [];
            this.selectedPipeline.stages.forEach((stage, stageIndex) => {
                formData.append('stages[' + stageIndex + '][id]', stage.id ?? 0);
                formData.append('stages[' + stageIndex + '][color]', stage.color ?? '#FFFFFF');
                formData.append('stages[' + stageIndex + '][pipeline_id]', this.selectedPipeline.id ?? null);
                formData.append('stages[' + stageIndex + '][name]', stage.name ?? '');
            });

            this.deletedStages.forEach((stageId, index) => {
                formData.append('deletedStages[' + index + ']', stageId ?? null);
            });

            axios.post('/pipeline/update', formData).then((response) => {
                if (response.data.errors.length > 0) {
                    response.data.errors.forEach((error) => {
                        ElNotification({
                            title: 'Не удалось обновить',
                            message: error,
                            type: 'error',
                            position: 'bottom-right',
                        });
                    });
                } else {
                    ElNotification({
                        title: 'Сохранено',
                        type: 'success',
                        position: 'bottom-right',
                    });

                    this.selectedPipeline = response.data.data;
                }
            });
        },
        createNewPipeline() {
            axios.post('/pipeline', { name: 'Новая' }).then((response) => {
                if (response.data.errors.length > 0) {

                } else {
                    this.selectedPipeline = response.data.data;
                    this.allPipelines.push(this.selectedPipeline);
                }
            });
        },
        deletePipeline(pipeline) {
            axios.delete('/pipeline/' + pipeline.id).then((response) => {
                if (response.data.errors.length > 0) {
                    response.data.errors.forEach((error) => {
                        ElNotification({
                            title: 'Не удалось удалить сделку',
                            message: error,
                            type: 'error',
                            position: 'bottom-right',
                        });
                    })
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
            let emptyExist = false;
            this.selectedPipeline.stages.forEach((item, index) => {
                if (item.name === '') {
                    emptyExist = true;
                }
            });

            if (!emptyExist) {
                this.selectedPipeline.stages.push(this.newStage);
            }

            this.newStage = { name: '', color: '' };
        },
        deleteStage(stage) {
            this.selectedPipeline.stages.forEach((item, index) => {
                if (item.id === stage.id) {
                    this.selectedPipeline.stages.splice(index, 1);
                    this.deletedStages.push(stage.id);
                }
            });
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
