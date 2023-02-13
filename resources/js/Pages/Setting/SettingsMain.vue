<template>
    <div class="wrap">
        <el-tabs>
            <el-tab-pane label="Сделки">
                <settings-deal
                    :pipelines="pipelines"
                />
            </el-tab-pane>
            <el-tab-pane
                label="Поля"
            >
                <settings-field
                    :fields="fields"
                />
            </el-tab-pane>

        </el-tabs>
    </div>
</template>

<script>
import SettingsDeal from "./SettingsDeal.vue";
import SettingsField from "./SettingsField.vue";
export default {
    name: 'SettingsMain',
    components: {
        SettingsDeal,
        SettingsField
    },
    props: {
        pipelines: {
            type: Array
        },
        settings: {
            type: Array
        },
        fields: {

        },
    },
    data() {
        return {
            newpipelines: this.pipelines || [],
        }
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
    height: 100%;
    padding: 10px;
}
.stage-container {
    padding: 10px;
}
.stage-item {
    cursor: pointer;
}
</style>
