<template>
    <div class="input-token d-flex flex-md-column">
        <label>Токен</label>
        <div class="d-flex d-inline-flex">
            <div class="input-token-container">
                <input v-model="token" type="text" name="token" class="form-control" disabled>
                <i class="las la-copy" @click="copyToken"></i>
            </div>
            <a class="btn btn-outline-primary ml-2" @click="tokenGenerate">Генерировать</a>
        </div>
    </div>

</template>

<script>
import {ElMessageBox, ElNotification} from "element-plus";

export default {
    name: "DetailUserTokenGenerate.vue",
    data() {
        return {
            token: '',
        }
    },
    methods: {
        tokenGenerate() {
            ElMessageBox.confirm(
                'Старый токен будет перезаписан',
                'Вы уверены?',
                {
                    confirmButtonText: 'Хорошо',
                    cancelButtonText: 'Отмена',
                    type: 'success',
                }
            ).then(() => {
                axios.get('token_generate').then((response) => {
                    if (response.data.token) {
                        this.token = response.data.token;
                    }
                });
            });
        },
        copyToken() {
            navigator.clipboard.writeText(this.token).then(() => {
                ElNotification({
                    title: 'Скопировано',
                    type: 'success',
                    position: 'bottom-right',
                });
            }).catch(err => {
                console.error('Error in copying text: ', err);
            });
        }
    }
}
</script>

<style scoped>
.input-token-container {
    position: relative;
    width: 100%;
}
.input-token-container > i {
    position: absolute;
    right: 10px;
    top: 13px;
    cursor: pointer;
}
a {
    cursor: pointer;
}
</style>
