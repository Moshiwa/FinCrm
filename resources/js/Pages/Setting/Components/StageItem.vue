<template>
    <div class="stage-item-container">

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
