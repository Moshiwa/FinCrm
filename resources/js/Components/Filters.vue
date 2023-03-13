<template>
    <div style="margin: 0 0 0 10px; display: inline-flex; gap: 10px;">
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
    </div>
</template>

<script>
export default {
    name: 'Filters',
    props: {
        filter: {
            type: Object,
            required: true
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
        }
    },
    methods: {
        appendQuery() {
            let url = window.location.pathname + "?";

            if (this.filterByType) {
                url += "&type=" + this.filterByType;
            }

            console.log(this.sortByDate);
            if (this.sortByDate) {
                url += "&date_sort=" + this.sortByDate;
            }

            window.location = url;
        }
    }
}
</script>

<style scoped>

</style>
