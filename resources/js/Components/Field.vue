<template>
    <div v-if="field.type.name === 'select'">
        <el-select
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
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
            :name="'fields['+ fieldIndex +'][value]'"
            type="date"
            @change="send"
        />
    </div>

    <div v-else-if="field.type.name === 'checkbox'">
        <el-checkbox
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            :label="field.name"
            @change="send"
        />
    </div>

    <div v-else-if="field.type.name === 'email'">
        <el-input
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            type="email"
            :label="field.name"
            :class="error ? 'error' : ''"
            @change="send"
            @input="check"
            @focus="check"
        />
    </div>
    <div v-else-if="field.type.name === 'phone'">
        <el-input
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            :label="field.name"
            :class="error ? 'error' : ''"
            @change="send"
            @input="check"
            @focus="check"
        />
    </div>
</template>

<script>
import {ElNotification} from "element-plus";

export default {
    name: 'Fields',
    emits: ['send'],
    props: {
        field: {
            type: Object,
            required: true
        },
        fieldIndex: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            error: false,
        }
    },
    methods: {
        check() {
            let field = this.field;
            this.error = false;
            if (field.is_required) {
                if (!field.pivot.value) {
                    this.error = true;
                }
            }
        },
        send() {
            if (this.error) {
                ElNotification({
                    title: 'Поле обязательно',
                    type: 'error',
                    position: 'bottom-right',
                });
            } else {
                this.$emit('update:modelValue', this.$el.innerHTML);
                this.$emit('send');
            }
        }
    }
}
</script>

<style scoped>
</style>
