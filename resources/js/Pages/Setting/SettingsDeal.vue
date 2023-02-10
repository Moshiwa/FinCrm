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
                        <el-select
                            class="w-50"
                            v-model="stage.status"
                            value-key="id"
                        >
                            <el-option
                                v-for="status in statuses"
                                :key="status.id"
                                :label="status.name"
                                :value="status"
                            />
                        </el-select>
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
    </div>
</template>

<script>
import StageItem from './Components/StageItem.vue'
export default {
    name: 'SettingsDeal',
    components: { StageItem },
    props: {
        pipelines: {
            type: Array
        },
        statuses: {
            type: Array
        }
    },
    data() {
        return {
            allPipelines: this.pipelines ?? [],
            selectedPipeline: this.pipelines[0] ?? {},
            stages: [],
            newStage: { name: '', color: '', status_id: '' }
        }
    },
    methods: {
        createNewPipeline() {
            axios.post('/settings/pipeline', { name: 'Новая' }).then((response) => {
                this.selectedPipeline = response.data;
                this.allPipelines.push(this.selectedPipeline);
            });
        },
        deletePipeline(pipeline) {
            axios.delete('/settings/pipeline/' + pipeline.id).then((response) => {
                this.allPipelines = response.data;
                this.selectedPipeline = response.data[response.data.length - 1] ?? {};
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
