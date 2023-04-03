<template>
<el-card v-if="integration.name === 'sms_center' && permissions.can_update">
    <sms-center :integration="integration" />
</el-card>

<el-card v-else-if="integration.name === 'uiscom'">
    <uiscom :integration="integration" :auth="auth"/>
</el-card>

<el-card v-else>
    <el-empty description="Интеграция не описана или у вас нет права доступа" />
</el-card>
</template>

<script>

import SmsCenter from "./SmsCenter.vue";
import Uiscom from "./Uiscom.vue";

export default {
    name: 'DetailIntegration',
    components: {
        SmsCenter,
        Uiscom,
    },
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
    mounted() {
        console.log(this.auth)
    },
    data() {
        return {
            permissions: {
                can_update: this.auth.permission_names.find((item) => item === 'sms_center.update'),
            }
        }
    },
}
</script>

<style scoped>

</style>
