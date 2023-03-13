<script>
export default {
    name: 'Helper',
    methods: {
        castFieldValue(fields) {
            fields.forEach((field, index) => {
                if (!!field.pivot?.value) {
                    if (field.pivot?.value === 'false' || field.pivot.value === 'true') {
                        fields[index].pivot.value = field.pivot.value === 'true';
                    } else {
                        fields[index].pivot.value = fields[index].pivot.value ?? '';
                    }
                } else {
                    let value = '';
                    if (field.type.name === 'checkbox') {
                        value = false;
                    }

                    field.pivot = { value: value};
                }
            });

            return fields;
        }
    }
}
</script>

<style scoped>

</style>
