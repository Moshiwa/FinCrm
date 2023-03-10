<template>
    <el-card class="report-item">
        <div class="report-title">
            {{ title }}
        </div>
        <div class="report-content">
            <div>
                <el-date-picker
                    v-model="datetime"
                    type="datetimerange"
                    start-placeholder="Start Date"
                    end-placeholder="End Date"
                    :default-time="defaultTime"
                />
            </div>
            <div class="report-button">
                <el-button
                    type="success"
                    @click="generateReport"
                >
                    Скачать
                </el-button>
            </div>
        </div>
    </el-card>
</template>

<script>
export default {
    name: 'ReportItem',
    props: {
        title: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            datetime: '',
            defaultTime: [
                new Date(2000, 1, 1, 12, 0, 0),
                new Date(2000, 2, 1, 8, 0, 0),
            ]
        }
    },
    methods: {
        generateReport() {
            this.$emit('generate', this.datetime);
        }
    }
}
</script>

<style scoped>
.reports-container {
    display: inline-flex;
    width: 100%;
    justify-content: space-between;
}
.report-item {
    width: 48%;
    box-sizing: border-box;
}
.report-content {
    display: inline-flex;
    gap: 5px;
    justify-content: space-between;
    width: 100%;
    align-items: center;
}
</style>
