<template>
    <div v-for="(field, index) in allFields">
        <el-collapse v-model="activeName" accordion>
            <el-collapse-item :title="field.name" :name="index">
                <div v-for="setting in field.settings">
                    <el-checkbox
                        v-model="setting.pivot.is_enable"
                        :checked="setting.pivot.is_enable"
                        :label="setting.description"
                    />
                </div>
            </el-collapse-item>
        </el-collapse>
    </div>
    <el-button
        type="primary"
        @click="save"
    >
        Сохранить
    </el-button>
</template>

<script>
export default {
    name: 'SettingsField',
    props: {
        fields: {
            type: Array
        }
    },
    mounted() {
        console.log(this.fields)
    },
    data() {
        return {
            activeName: '1',
            allFields: this.fields ?? [],
        }
    },
    methods: {
        save() {
            console.log(this.allFields)
            axios.post('/field/save', this.allFields).then((response) => {

            });
        }
    }
}
</script>

<style scoped>

</style>
