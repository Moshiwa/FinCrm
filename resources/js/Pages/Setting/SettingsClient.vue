<template>
    <div class="w-50">

        <selected-field
            :fields="fields"
        />
    </div>
</template>

<script>
import SelectedField from '../Deal/Components/SelectedField.vue'
export default {
    name: 'SettingsClient',
    components: { SelectedField },
    props: {
        fields: {
            type: Array
        }
    },
    data() {
        return {
            selectedField: {}
        }
    }

}
</script>

<style scoped>
.custom-field__container {
    display: flex;
    flex-direction: row;
    gap: 10px;
    justify-content: space-between;
}
</style>
