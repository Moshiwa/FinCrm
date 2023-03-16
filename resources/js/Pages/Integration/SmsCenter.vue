<template>
    <el-form-item label="Логин">
        <el-input />
    </el-form-item>

    <el-form-item label="Пароль">
        <el-input type="password"/>
    </el-form-item>

    <el-button type="success">Сохранить</el-button>

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
    }
}
</script>

<style scoped>

</style>
