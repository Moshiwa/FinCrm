<template>
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
</template>

<script>
import {ElNotification} from "element-plus";

export default {
    name: "TelephonyAuthFields.vue",
    props: {
        entry: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            login: '',
            password: '',
        }
    },
    mounted() {

    },
    methods: {
        getManagerId() {
            axios.post('/admin/integration/uiscom/manager-id', {
                user_id: this.entry.id,
                login: this.login,
                password: this.password,
            }).then((response) => {
                if (response.data.success === true) {
                    let element = document.getElementsByName('uiscom_employee_id')[0]
                    element.value = response.data.data;
                    ElNotification({
                        title: 'Успех',
                        type: 'success',
                    });
                } else {
                    ElNotification({
                        title: response.data.message,
                        type: 'error',
                        position: 'bottom-right',
                    });
                }
            }).catch((response) => {
                ElNotification({
                    title: 'Ошибка',
                    type: 'error',
                    position: 'bottom-right',
                });
            });
        },
    }
}
</script>

<style scoped>

</style>
