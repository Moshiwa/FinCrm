<template>
    <h1>{{ stage.name }}</h1>

    <div class="settings-container">
        <div class="settings-group">
            <div>Кнопки</div>
            <div v-for="(setting, settingIndex) in stage.settings">

                <el-form-item
                    v-if="setting.field.type === 'select'"
                    :label="setting.name"
                    prop="stage.settings"
                >
                   <el-select
                       v-model="setting.field.value"
                       :multiple="setting.field.multiple"
                       :name="'settings['+ setting.id +']'"
                       @change="save"
                   >
                       <el-option
                            v-for="(item, index) in setting.field.options"
                            :key="index"
                            :label="item"
                            :value="index"
                       />
                   </el-select>
                </el-form-item>

                <el-form-item
                    v-else
                    prop="stage.settings"
                >
                    <el-checkbox
                        v-model="setting.field.value"
                        :label="setting.description"
                        :name="'settings['+ setting.id +']'"
                        @change="save"
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
        },
    },
    data() {
        return {
            stage: this.entry ?? {},
            stages: this.stages ?? [],
        }
    },
    mounted() {
        this.stage.settings = this.settings;
    },
    methods: {
        save() {

            axios.post('/admin/stage/update', this.stage)
        }
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
