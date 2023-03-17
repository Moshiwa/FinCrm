<template>
    <el-form-item label="Логин">
        <el-input
            v-model="thisIntegration.login"
            autocomplete="off"
        />
    </el-form-item>

    <el-form-item label="Пароль">
        <el-input
            v-model="thisIntegration.password"
            autocomplete="off"
            type="password"
        />
    </el-form-item>

    <el-button type="success" @click="save">Сохранить</el-button>

    <el-divider content-position="center">Инструкция</el-divider>
    1. Используйте ваш логин
    <br>
    2. Установите пароль для API <a href="https://smsc.ru/passwords/">тут</a>, не используйте пароль от аккаунта

</template>

<script>
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
            thisIntegration: this.integration ?? {}
        }
    },
    methods: {
        save() {
            axios.post('/admin/integration/save/', this.thisIntegration)
        }
    }
}
</script>

<style scoped>

</style>
