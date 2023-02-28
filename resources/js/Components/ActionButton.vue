<template>
    <div
        class="button-setting"
        :class="'btn-custom__' + button.color"
    >
        <i :class="'las la-' + button.icon"></i>
        {{ button.name }}
    </div>
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
    width: 200px;
    border-radius: 2px;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    padding: 0.375rem 0.75rem;
    text-align: center;
}
.button-setting:hover {
    border-radius: 2px;
    box-shadow: 0 3px 8px 0 rgb(0 0 0 / 38%);
    filter: brightness(97%);
}

</style>
