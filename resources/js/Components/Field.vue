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

    <div v-else-if="field.type.name === 'number'">
        <el-input-number
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
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
        >

        </el-input>
    </div>
    <div v-else-if="field.type.name === 'address'">
        <el-select
            v-model="field.pivot.value"
            :name="'fields['+ fieldIndex +'][value]'"
            filterable
            remote
            allow-create
            reserve-keyword
            @change="send"
            :remote-method="findAddress"
            @focus="check"
        >
            <el-option
                v-for="(option, index) in addresses"
                :key="index"
                :label="option.value"
                :value="option.value"
            />
        </el-select>
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
            addresses: [],
            timer: false,
        }
    },
    methods: {
        findAddress(query) {
            query = !!query ? query : this.field.pivot.value;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                axios.get('/admin/field/find-address?search=' + query)
                    .then((response) => {
                        this.addresses = response.data.data.suggestions
                    })
            }, 1000)
        },
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
