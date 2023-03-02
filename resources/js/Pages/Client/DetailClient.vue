<template>
    <div>
        <el-form-item label="Имя" prop="client.name">
            <el-input
                name="name"
                v-model="client.name"
            />
        </el-form-item>
        <div v-for="(field, fieldIndex) in client.fields">
            <el-form-item
                :label="field.name"
                :required="field.is_required"
            >
                <field :field="field" :field-index="fieldIndex"/>
            </el-form-item>
            <el-input
                type="hidden"
                :name="'fields['+ fieldIndex +'][id]'"
                v-model="field.id"
            />
            <el-input
                type="hidden"
                :name="'fields['+ fieldIndex +'][is_required]'"
                v-model="field.is_required"
            />
        </div>
    </div>
</template>

<script>
import Field from "../../Components/Field.vue";
export default {
    name: 'DetailClient.vue',
    components: {
        Field,
    },
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
