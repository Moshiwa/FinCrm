<template>
    <h1>{{ stage.name }}</h1>

    <div class="settings-container">
        <div class="settings-group">
            <div>Кнопки</div>
            <div v-for="(setting, settingIndex) in stage.settings">
                <el-form-item prop="stage.settings">
                    <el-checkbox
                        v-if="setting.field === 'button'"
                        v-model="setting.pivot.is_active"
                        :checked="setting.pivot.is_active"
                        :label="setting.description"
                        :name="'settings['+ setting.id +']'"
                    />
                </el-form-item>
            </div>
        </div>

        <div class="settings-group">
            <div>Другие</div>
            <div v-for="setting in stage.settings">
                <el-form-item prop="stage.settings">
                    <el-checkbox
                        v-if="setting.field === 'other'"
                        v-model="setting.pivot.is_active"
                        :checked="setting.pivot.is_active"
                        :label="setting.description"
                        :name="'settings['+ setting.id +']'"
                    />
                </el-form-item>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: 'StageSettings',
    props: {
        entry: {
            type: Object,
            default: {}
        },
        settings: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            stage: this.entry ?? {},
        }
    },
    mounted() {
        this.stage.settings = this.settings;
    }
}
</script>

<style scoped>
.settings-container {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 15px;

}
</style>
