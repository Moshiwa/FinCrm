<template>
<el-card v-if="integration.name === 'sms_center'">
    <sms-center :integration="integration" />
</el-card>

<el-card v-else-if="integration.name === 'uiscom'">
    <uiscom :integration="integration" />
</el-card>

    <el-card v-else>
        <el-empty description="Интеграция не описана, обратитесь к разработчикам" />
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
        }
    },
    mounted() {
        console.log(this.integration)
    },
    data() {

    },
}
</script>

<style scoped>

</style>
