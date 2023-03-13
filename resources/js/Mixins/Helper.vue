<script>
export default {
    name: 'Helper',
    methods: {
        castFieldValue(fields) {
            fields.forEach((field, index) => {
                if (field.pivot?.value === 'false' || field.pivot.value === 'true') {
                    fields[index].pivot.value = field.pivot.value === 'true';
                }
            });

            return fields;
        }
    }
}
</script>

<style scoped>

</style>
