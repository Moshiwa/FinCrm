<template>
    <el-tooltip
        class="box-item"
        effect="dark"
        :content="button.name"
        placement="top-start"
    >
        <div
            class="button-setting"
            :class="'btn-custom__' + button.color"
            @click="this.$emit('click')"
        >
            <i :class="'las la-' + button.icon"></i>
            {{ button.name }}
        </div>
    </el-tooltip>
</template>

<script>
export default {
    name: 'ActionButton',
    props: {
        button: {
            type: Object,
            required: true
        }
    }
}
</script>

<style scoped>
.button-setting {
    cursor: pointer;
    width: 160px;
    border-radius: 2px;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    padding: 0.5rem 0.5rem;
    text-align: center;

    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.button-setting:hover {
    border-radius: 2px;
    box-shadow: 0 3px 8px 0 rgb(0 0 0 / 38%);
    filter: brightness(97%);
}

</style>
