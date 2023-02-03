<template>
    <div class="wrap">
        <div class="col-md-5 bold-labels">
            <div class="card">
                <div class="card-body row">
                    <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <div class="row-label">Наименование</div>
                        <contenteditable
                            v-model="deal.name"
                            @send="send"
                        />
                    </el-row>
                    <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <div class="row-label">Комментарий</div>
                        <contenteditable
                            v-model="deal.comment"
                            @send="send"
                        />
                    </el-row>
                    <hr>
                    <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <div class="row-label">Имя</div>
                        <contenteditable
                            v-model="deal.client.name"
                            @send="send"
                        />
                    </el-row>
                    <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                        <div class="row-label">Телефон</div>
                        <contenteditable
                            v-model="deal.client.phone"
                            @send="send"
                        />
                    </el-row>
                </div>
            </div>
        </div>

        <div class="col-md-4 bold-labels">
            <div class="card">
                <div class="card-body row">
                    <el-row
                        :gutter="20"
                        class="ml-3 w-35 text-gray-600 inline-flex data-row"
                        v-for="comment in deal.comments"
                    >
                        {{ comment.text }}
                    </el-row>
                </div>
            </div>
        </div>

        <div class="col-md-2 bold-labels">
            <div class="card">
                <div class="card-body row">
                    <span>Смена воронки</span>
                    <el-select
                        v-model="deal.pipeline"
                        size="small"
                        @change="changePipeline"
                    >
                        <el-option
                            v-for="item in pipelines"
                            :key="item.id"
                            :label="item.name"
                            :value="item"
                        />
                    </el-select>
                    <hr>
                </div>
                <div class="card-body row">
                    <span>Смена стадии</span>
                    <el-select v-model="deal.stage" size="small">

                    </el-select>
                    <hr>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

import { ElInput } from 'element-plus';

export default {
    name: 'DetailDeal',
    components: [
    ],
    props: {
        deal: {
            type: [Array, Object],
            default: null
        },
        pipelines: {
            type: [Array, Object],
            default: null
        }
    },
    mounted() {
        console.log(this.deal)
        console.log(this.pipelines);
    },
    data() {
        return {

        }
    },
    methods: {
        changePipeline(item) {
            console.log(item.id)
        },
        send() {
            console.log(this.deal);
            axios.post('/deal/update', this.deal)
        },
    }
}
</script>

<style scoped>
.wrap {
    display:flex;
    flex-direction: row;
}

.bold-labels {
    padding-left: 0px;
}
.row-label {
    min-width: 125px;
}
.data-row {
    display: flex;
    width: 100%;
    gap: 10px
}
</style>
