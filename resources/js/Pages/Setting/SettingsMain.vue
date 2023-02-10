<template>
    <div class="wrap">
        <el-tabs>
            <el-tab-pane label="Сделки">
                <settings-deal
                    :pipelines="pipelines"
                    :statuses="statuses"
                />
            </el-tab-pane>
            <el-tab-pane label="Этапы">

            </el-tab-pane>
            <el-tab-pane label="Клиенты">
                <settings-client
                    :fields="fields"
                />
            </el-tab-pane>
        </el-tabs>
<!--        <el-tabs tab-position='left'>
            <el-tab-pane  v-for="pipeline in newpipelines" :label="pipeline.name">
                <div class="stage-container">
                    {{ pipeline.name }}
                    <hr>
                    Стадии
                    <el-tabs type="card" >
                        <el-tab-pane v-for="(stage, index) in pipeline.stages" :label="stage.name">
                            <div v-for="setting in mergeSettings(stage)">
                                <el-checkbox
                                    :label="setting.description"
                                    :checked="setting.pivot?.is_enable"
                                    @change="selectSetting($event, setting)"
                                />
                            </div>
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </el-tab-pane>
        </el-tabs>-->
    </div>
</template>

<script>
import SettingsDeal from "./SettingsDeal.vue";
import SettingsClient from "./SettingsClient.vue";
export default {
    name: 'SettingsMain',
    components: {
        SettingsDeal,
        SettingsClient
    },
    props: {
        pipelines: {
            type: Array
        },
        settings: {
            type: Array
        },
        statuses: {

        },
        fields: {

        },
    },
    data() {
        return {
            newpipelines: this.pipelines || [],
        }
    },
    mounted() {
        console.log(this.pipelines)
    },
    methods: {
        selectStage(stage) {
            alert('Вы выбрали' + stage.id)
        },
        addNewPipeline() {
            console.log('1')
            this.newpipelines.push({ name: 'new', stages: [] })
        },
        selectSetting(event, setting) {
            if (!setting.pivot) {
                setting.pivot.is_enable = false
            }
            setting.pivot.is_enable = event;
            axios.post('/settings/save', setting);
        },
        mergeSettings(stage) {
            let stageSettings = stage.settings;
            let newSettings = [];
            this.settings.forEach((setting, index) => {
                newSettings.push(setting);
                stageSettings.forEach((stageSetting) => {
                    if (stageSetting.id === setting.id) {
                        newSettings[index] = stageSetting;
                    }
                })
            });

            return newSettings;
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
    padding: 10px;
}
.stage-container {
    padding: 10px;
}
.stage-item {
    cursor: pointer;
}
</style>
