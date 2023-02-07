<template>
    <el-select
        v-model="selectedField"
        value-key="id"
    >
        <el-option
            v-for="field in fields"
            :key="field.id"
            :label="field.name"
            :value="field"
        />
    </el-select>

    <div v-if="selectedField.type === 'select'">
        <el-select
            v-model="fieldValue"
        >
            <el-option
                v-for="(option, index) in selectedField.options"
                :key="index"
                :label="option"
                :value="option"
            />
        </el-select>
    </div>
    <div v-else-if="selectedField.type === 'date'">
        <el-input
            v-model="fieldValue"
            type="date"
        />
    </div>
    <div v-else>
        <el-input
            v-model="fieldValue"
        />
    </div>
    <el-button @click="save">Сохранить</el-button>
</template>

<script>
export default {
    name: 'SelectedField',
    props: {
        fields: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            selectedField: { id: '', name: '', type: '', options: {}, pivot: { value: '' }},
            fieldValue: '',
        }
    },
    methods: {
        save() {
            let data = {
                value: this.fieldValue,
                field: this.selectedField
            }
            this.$emit('updateField', data);
            this.fieldValue = '';
        }
    }
}
</script>

<style scoped>

</style>
