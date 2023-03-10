<template>
    <div class="reports-container">
        <report-item
            title="Отчет по пользователям"
            @generate="generateReport($event, 'user_report')"
        />
        <report-item
            title="Отчет по сделкам"
            @generate="generateReport($event, 'deal_report')"
        />
        <report-item
            title="Отчет по клиентам"
            @generate="generateReport($event, 'client_report')"
        />
    </div>
</template>

<script>
import ReportItem from "../../Components/ReportItem.vue";
export default {
    name: 'DetailReport',
    components: { ReportItem },
    props: [],
    data() {
        return {

        }
    },
    methods: {
        generateReport(dates, type) {
            if (! type) {
                return 0;
            }

            let result = [];
            if (dates.length > 0) {
                dates.forEach((date) => {
                    result.push((date.getTime() / 1000));
                });
            }

            let start = result[0] ?? '';
            let end = result[1] ?? '';

            let url = '/admin/report/generate?type=' + type;
            url += !!start ? '&start=' + start : '';
            url += !!end ? '&end=' + end : '';

            axios.get(url)
            .then((response) => {
                console.log(response)
            });
        }
    }
}
</script>

<style scoped>
.reports-container {
    display: inline-flex;
    width: 100%;
    justify-content: flex-start;
    flex-wrap: wrap;
    gap: 15px;
}

</style>
