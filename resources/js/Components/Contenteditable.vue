<template>
    <div
        class="contenteditable"
        contenteditable
        @focusout="send"
        @keydown="blockLineBreak"
    >
        {{ modelValue }}
    </div>
</template>

<script>
export default {
    name: 'ContentEditable',
    props: {
        modelValue: {
            type: String,
            default: '',
            required: true
        },
    },
    methods: {
        blockLineBreak(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                e.target.blur();
                window.getSelection().removeAllRanges();
            }
        },
        send(e) {
            this.$emit('update:modelValue', this.$el.innerHTML);
            this.$emit('send');
        }
    }
}
</script>

<style scoped>

</style>
