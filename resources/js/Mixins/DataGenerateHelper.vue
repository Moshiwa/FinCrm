<script>
export default {
    name: 'DataGenerateHelper',
    methods: {
        dealFormData(deal, action, deleteCommentId) {
            const formData = new FormData();

            formData.append('id', deal.id);
            formData.append('name', deal.name);
            formData.append('pipeline_id', deal.pipeline.id);
            formData.append('stage_id', deal.stage.id);
            formData.append('responsible_id', deal.responsible.id);
            formData.append('client_id', deal.client_id);

            formData.append('comment_count', deal.comments.length ?? 0);

            deal.all_fields = deal.all_fields ?? [];
            deal?.all_fields.forEach((field, fieldIndex) => {
                formData.append('fields[' + field.id + '][value]', field.pivot?.value ?? '');
            });

            deal.client = deal.client ?? [];
            formData.append('client[name]', deal.client?.name);
            deal.client.all_fields = deal.client?.all_fields ?? [];
            deal.client?.all_fields.forEach((field, fieldIndex) => {
                formData.append('client[fields][' + field.id + '][value]', field.pivot?.value ?? '');
            });

            deal.comments = deal.comments ?? [];
            deal?.comments.forEach((comment, commentIndex) => {
                if (!comment.id) {
                    formData.append('new_comment[id]', comment.id ?? '');
                    formData.append('new_comment[deal_id]', deal.id);
                    formData.append('new_comment[type]', comment.type);
                    formData.append('new_comment[content]', comment.content);
                    comment.files = comment.files ?? [];
                    comment.files.forEach((file, fileIndex) => {
                        formData.append('new_comment[files][' + fileIndex + ']', file);
                    })
                }
            });

            if (!!deleteCommentId) {
                formData.append('delete_comment_id', deleteCommentId);
            }

            if (!!action?.id) {
                formData.append('change_custom_field[field_id]', action.id);
                if (!! action?.pivot?.client_id) {
                    formData.append('change_custom_field[client_id]', deal.client.id);
                } else {
                    formData.append('change_custom_field[deal_id]', deal.id);
                }
                formData.append('change_custom_field[value]', action.pivot.value);
            }

            return formData;
        },
        dealPrepareDataByButtonOptions(action, deal) {
            deal.pipeline.id = !!action.pipeline_id ? action.pipeline_id : deal.pipeline.id;
            deal.stage.id = !!action.stage_id ? action.stage_id : deal.stage.id;
            deal.responsible.id = !!action.responsible_id ? action.responsible_id : deal.responsible.id;

            return deal;
        }
    }
}
</script>

