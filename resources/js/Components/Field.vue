<template>
    <div v-if="field.type.name === 'select'">
        <el-select
            v-model="field.pivot.value"
            @change="send"
        >
            <el-option
                v-for="(option, index) in field.options"
                :key="index"
                :label="option.value"
                :value="option.value"
            />
        </el-select>
    </div>

    <div v-else-if="field.type.name === 'date'">
        <el-input
            v-model="field.pivot.value"
            type="date"
            @change="send"
        />
    </div>

    <div v-else-if="field.type.name === 'checkbox'">
        <el-checkbox
            v-model="field.pivot.value"
            :label="field.name"
            @change="send"
        />
    </div>

    <div v-else-if="field.type.name === 'email'">
        <el-input
            v-model="field.pivot.value"
            type="email"
            :label="field.name"
            @change="send"
        />
    </div>

    <div v-else>
        <el-input
            v-model="field.pivot.value"
            :label="field.name"
            @change="send"
        />
    </div>


</template>

<script>

export default {
    name: 'Fields',
    props: {
        field: {
            type: Object,
            required: true
        }
    },
    mounted() {
        console.log(this.field)
    },
    methods: {
        send(e) {
            this.$emit('update:modelValue', this.$el.innerHTML);
            this.$emit('send');
        }
    }
}
</script>

<style scoped>

</style>
