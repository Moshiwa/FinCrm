<template>
    <el-form-item label="Токен">
        <el-input
            v-model="token"
            autocomplete="off"
        />
    </el-form-item>
    <el-form-item label="Виртуальный номер телефона">
        <el-input
            v-model="virtualNumber"
            autocomplete="off"
        />
    </el-form-item>
    <el-divider/>
    <el-form-item label="Идентификатор менеджера">
        <el-input
            v-model="employeeId"
            autocomplete="off"
        />
    </el-form-item>

    <el-drawer v-model="visibleGetIdForm" :show-close="false">
        <template #header="{ close, titleId, titleClass }">
            <h4 :id="titleId" :class="titleClass">Выберите файлы</h4>
        </template>
        <el-form-item label="Логин">
            <el-input
                v-model="login"
                autocomplete="off"
            />
        </el-form-item>
        <el-form-item label="Пароль">
            <el-input
                v-model="password"
                autocomplete="off"
            />
        </el-form-item>
        <el-button @click="getManagerId">Отправить</el-button>
    </el-drawer>

    <el-button @click="visibleGetIdForm = true">Получить идентификатор менеджера</el-button>


    <el-button type="success" @click="save">Сохранить</el-button>

    <el-divider content-position="center">Инструкция</el-divider>
    1. Как получить токен
    <br>
    <img src="/assets/images/info/uiscom_get_token.png"/>
    <br>
    2. Альтернативный способ получения Идентификатора пользователя
    <br>
    <img src="/assets/images/info/uiscom_get_id.png"/>
    <br>

</template>

<script>
export default {
    name: 'Uiscom',
    props: {
        integration: {
            type: Object,
            required: true
        },
        auth: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            visibleGetIdForm: false,
            employeeId: this.auth.uiscom_employee_id ?? '',
            token: this.auth.uiscom_token ?? '',
            virtualNumber: this.auth.uiscom_virtual_number ?? '',
            login: '',
            password: '',
        }
    },
    methods: {
        getManagerId() {
            axios.post('/admin/integration/uiscom/manager-id', {
                login: this.login,
                password: this.password,
            }).then((response) => {
                if (response.data.success === true) {
                    this.employeeId = response.data.data
                    this.visibleGetIdForm = false;
                }
            });
        },
        save() {
            axios.post('/admin/integration/uiscom/save', {
                token: this.token,
                employee_id: this.employeeId,
                virtual_number: this.virtualNumber
            }).then((response) => {

            });
        }
    }
}
</script>

<style scoped>

</style>
