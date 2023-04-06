<script>
export default {
    name: 'DataGenerateHelper',
    methods: {
        dealFormData(deal, action, deleteCommentId) {
            const formData = new FormData();

            formData.append('id', deal.id);
            formData.append('name', deal.name);
            formData.append('deadline', deal.deadline);
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

            if (!!action.deadline) {
                let currentSeconds = Math.floor(Date.now() / 1000);
                let newDeadline = action.deadline + currentSeconds;
                let dateObj = new Date(newDeadline * 1000);

                let date = new Date(dateObj.getTime() - (dateObj.getTimezoneOffset() * 60000 ))
                    .toISOString()
                    .split("T")[0];
                deal.deadline = `${date} ${dateObj.getHours()}:${dateObj.getMinutes()}`;
            }

            return deal;
        },
        taskFormData(task, action, deleteCommentId) {
            const formData = new FormData();
            if (!!task.id) {
                formData.append('id', task.id);
            }
            if (!!task.name) {
                formData.append('name', task.name);
            }
            if (!!task.description) {
                formData.append('description', task.description);
            }
            if (!!task.deadline) {
                formData.append('deadline', task.deadline);
            }
            if (!!task.stage?.id) {
                formData.append('task_stage_id', task.stage.id);
            }
            if (!!task.responsible?.id) {
                formData.append('responsible_id', task.responsible.id);
            }
            if (!!task.manager?.id) {
                formData.append('manager_id', task.manager.id);
            }
            if (!!task.executor?.id) {
                formData.append('executor_id', task.executor.id);
            }

            formData.append('comment_count', task.comments.length ?? 0);
            if (!!deleteCommentId) {
                formData.append('delete_comment_id', deleteCommentId);
            }

            task.all_fields = task.all_fields ?? [];
            task?.all_fields.forEach((field, fieldIndex) => {
                formData.append('fields[' + field.id + '][value]', field.pivot?.value ?? '');
            });

            task.comments = task.comments ?? [];
            task?.comments.forEach((comment, commentIndex) => {
                if (!comment?.id) {
                    formData.append('new_comment[id]', comment.id ?? '');
                    formData.append('new_comment[task_id]', task.id);
                    formData.append('new_comment[type]', comment.type);
                    formData.append('new_comment[content]', comment.content);
                    comment.files = comment.files ?? [];
                    comment.files.forEach((file, fileIndex) => {
                        formData.append('new_comment[files][' + fileIndex + ']', file);
                    })
                }
            });

            console.log(action)
            if (!!action?.id) {
                formData.append('change_custom_field[field_id]', action.id);
                formData.append('change_custom_field[task_id]', task.id);
                formData.append('change_custom_field[value]', action.pivot.value);
            }

            return formData;
        },
        taskPrepareDataByButtonOptions(action, task) {
            task.stage.id = !!action.task_stage_id ? action.task_stage_id : task.stage?.id;
            task.responsible = !!action.responsible_id ? {id: action.responsible_id} : task.responsible;
            task.manager = !!action.manager_id ? {id: action.manager_id} : task.manager;
            task.executor = !!action.executor_id ? {id: action.executor_id} : task.executor;

            if (!!action.deadline) {
                let currentSeconds = Math.floor(Date.now() / 1000);
                let newDeadline = action.deadline + currentSeconds;
                let dateObj = new Date(newDeadline * 1000);

                let date = new Date(dateObj.getTime() - (dateObj.getTimezoneOffset() * 60000 ))
                    .toISOString()
                    .split("T")[0];
                task.deadline = `${date} ${dateObj.getHours()}:${dateObj.getMinutes()}`;
            }

            return task;
        },
    }
}
</script>

