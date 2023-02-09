<template>
    <div class="wrap">
        <el-tabs tab-position='left'>
            <el-tab-pane  v-for="pipeline in pipelines" :label="pipeline.name">
                <div class="stage-container">
                    {{ pipeline.name }}
                    <hr>
                    Стадии
                    <el-tabs type="card" >
                        <el-tab-pane v-for="stage in pipeline.stages" :label="stage.name">
                            настройки для  стадии {{ stage.name }}
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
export default {
    name: 'SettingsMain',
    props: {
        pipelines: {
            type: Array
        }
    },
    methods: {
        selectStage(stage) {
            alert('Вы выбрали' + stage.id)
        }
    }
}
</script>

<style scoped>
.wrap {
    background: white;
    border-radius: 4px;
    width: 100%;
    height: 100vh;
}
.stage-container {
    padding: 10px;
}
.stage-item {
    cursor: pointer;
}
</style>
