<template>
    <div style="margin: 0 0 0 10px; display: inline-flex; gap: 10px;">
        <el-select
            v-model="sortByDate"
            clearable
            @change="appendQuery"
        >
            <el-option
                v-for="item in sortByDateOption"
                :key="item.type"
                :label="item.label"
                :value="item.type"
            />
        </el-select>
        <el-select
            v-model="filterByType"
            clearable
            @change="appendQuery"
        >
            <el-option
                v-for="item in filterByTypeOption"
                :key="item.type"
                :label="item.label"
                :value="item.type"
            />
        </el-select>
        <el-date-picker
            v-if="daterangeFilter"
            v-model="datetime"
            type="datetimerange"
            start-placeholder="Дата создания (с)"
            end-placeholder="Дата создания (до)"
            :default-time="defaultTime"
            @change="appendQuery"
        />
    </div>
</template>

<script>
export default {
    name: 'Filters',
    props: {
        filter: {
            type: Object,
            required: true
        },
        daterangeFilter: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            filterByType: this.filter.type ?? null,
            filterByTypeOption: [
                {
                    label: 'Документы',
                    type: 'document'
                },
                {
                    label: 'Комментарии',
                    type: 'comment'
                },
                {
                    label: 'События',
                    type: 'action'
                },
                {
                    label: 'Аудио',
                    type: 'audio'
                },

            ],
            sortByDate: this.filter.sort ?? 'desc',
            sortByDateOption: [
                {
                    label: 'По возврастанию (дата создания)',
                    type: 'asc'
                },
                {
                    label: 'По убыванию (дата создания)',
                    type: 'desc'
                },
            ],
            datetime: '',
            defaultTime: [
                new Date(2000, 1, 1, 12, 0, 0),
                new Date(2000, 2, 1, 8, 0, 0),
            ]
        }
    },
    methods: {
        appendQuery() {
            let result = [];
            if (this.datetime.length > 0) {
                this.datetime.forEach((date) => {
                    result.push((date.getTime() / 1000));
                });
            }

            let start = result[0] ?? '';
            let end = result[1] ?? '';
            let url = window.location.pathname + "?";

            if (!!this.filterByType) {
                url += "&type=" + this.filterByType;
            }
            if (!!this.sortByDate) {
                url += "&date_sort=" + this.sortByDate;
            }
            if (!!start) {
                url += "&start=" + start;
            }
            if (!!end) {
                url += "&end=" + end;
            }

            window.location = url;
        }
    }
}
</script>

<style scoped>

</style>
