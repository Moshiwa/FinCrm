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
import {computed} from "vue";

export default {
    name: 'ContentEditable',
    props: {
        modelValue: {
            type: String,
            default: '',
            required: true
        }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const message = computed({
            get: () => props.modelValue,
            set: (val) => emit('update:modelValue', val),
        });

        return { message };
    },
    methods: {
        blockLineBreak(e) {
            if (e.keyCode === 13) {
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
