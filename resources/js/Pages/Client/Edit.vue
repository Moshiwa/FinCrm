<template>

        <el-form-item label="Имя" prop="client.name">
            <el-input
                name="name"
                v-model="client.name"
            />
        </el-form-item>
        <div v-for="(field, fieldIndex) in client.fields">
            <el-form-item
                v-if="field.type === 'select'"
                :label="field.name"
            >
                <el-select
                    v-model="field.pivot.value"
                    :name="'fields['+ fieldIndex +'][value]'"
                >
                    <el-option
                        v-for="(option, index) in field.options"
                        :key="index"
                        :value="option.value"
                        :label="option.value"
                    />
                </el-select>
            </el-form-item>
            <el-form-item v-else :label="field.name" prop="field.*.value">
                <el-input
                    :name="'fields['+ fieldIndex +'][value]'"
                    v-model="field.pivot.value"
                />
            </el-form-item>

            <el-input
                type="hidden"
                :name="'fields['+ fieldIndex +'][id]'"
                v-model="field.id"
            />
        </div>
</template>

<script>
export default {
    name: 'ClientEdit.vue',
    props: {
        fields: {
            type: Array,
            default: [],
        },
        entry: {
            type: Object,
            default: {}
        }
    },
    data() {
        return {
            client: this.entry ?? {},
        }
    },
    mounted() {
        this.client.fields = this.fields;
    },
    methods: {
    }
}
</script>

<style scoped>

</style>
