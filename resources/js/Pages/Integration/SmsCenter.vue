<template>
    <el-form-item label="Тема письма">
        <el-input
            v-model="thisIntegration.data.theme"
        />
    </el-form-item>

    <el-form-item label="Отправитель">
        <el-select
            v-model="thisIntegration.data.sender"
            autocomplete="off"
        >
            <el-option
                v-for="(option, index) in senders"
                :key="index"
                :label="option.sender"
                :value="option.sender"
            />
        </el-select>
    </el-form-item>

    <el-button type="success" @click="save">Сохранить</el-button>

    <el-divider content-position="center">Инструкция</el-divider>
    Добавить отправителя можно <a href="https://smsc.ru/mail_senders/">здесь</a>

</template>

<script>
import {ElNotification} from "element-plus";

export default {
    name: 'SmsCenter',
    props: {
        integration: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            thisIntegration: this.integration ?? {},
            senders: []

        }
    },
    created() {
        axios.get('/admin/integration/smsc/senders').then((response) => {
            this.senders = response.data.data ?? [];
        });
    },
    methods: {
        save() {
            axios.post('/admin/integration/smsc/save/', this.thisIntegration).then((response) => {
                if (response.data.success === false) {
                    ElNotification({
                        title: response.data.message,
                        type: 'error',
                        position: 'bottom-right',
                    });
                } else {
                    ElNotification({
                        title: 'Успех',
                        type: 'success',
                    })
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
