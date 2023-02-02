<template>
    <div class="wrap">
    <div class="col-md-5 bold-labels">
        <div class="card">
            <div class="card-body row">
                <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                    <div>Комментарий</div>
                    <div class="contenteditable" data-field="deal.comment" contenteditable> {{ deal.comment }} </div>
                </el-row>

            </div>
        </div>
    </div>

    <div class="col-md-5 bold-labels">
        <div class="card">
            <div class="card-body row">
                <el-row :gutter="20" class="ml-3 w-35 text-gray-600 inline-flex data-row">
                    <div>История</div>
                </el-row>
            </div>
        </div>
    </div>

    <div class="col-md-2 bold-labels">
        Buttons
    </div>
    </div>

</template>

<script>

import { ElInput } from 'element-plus';
export default {
    name: "DetailDeal",
    props: {
        deal: {
            type: [Array, Object],
            default: null
        },
    },
    data() {
        return {
        }
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

.data-row {
    display: flex;
    width: 100%;
    gap: 10px
}
</style>
