<template>
    <div class="custom-field__container">
        <el-select
            v-model="selectedField"
            value-key="id"
            @change="openInput"
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
                :disabled="inputDisabled"
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
                :disabled="inputDisabled"
                v-model="fieldValue"
                type="date"
            />
        </div>
        <div v-else>
            <el-input
                :disabled="inputDisabled"
                v-model="fieldValue"
            />
        </div>
        <el-button @click="save">Сохранить</el-button>
    </div>
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
            inputDisabled: true,
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
            this.inputDisabled = true;
        },
        openInput() {
            this.fieldValue = '';
            this.inputDisabled = false;
        }
    }
}
</script>

<style scoped>
.custom-field__container {
    display: flex;
    flex-direction: row;
    gap: 10px;
}
</style>
