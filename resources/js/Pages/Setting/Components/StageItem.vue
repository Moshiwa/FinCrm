<template>
    <div class="stage-item-container">
        <el-input
            class="w-50"
            v-model="stage.name"
        />
        <el-select
            class="w-50"
            v-model="stage.status"
            value-key="id"
        >
            <el-option
                v-for="status in statuses"
                :key="status.id"
                :label="status.name"
                :value="status"
            />
        </el-select>
        <el-color-picker v-model="stage.color" />
        <el-button
        @click="deleteStage(stage)"
        type="danger"
    >
        <el-icon class="el-icon--right">
            <Delete />
        </el-icon>
    </el-button>
    </div>
</template>

<script>
export default {
    name: 'StageItem',
    props: {
        stage: {
            type: Object
        }
    }
}
</script>

<style scoped>
.stage-item-container {
    display: inline-flex;
    gap: 10px;
}
</style>
