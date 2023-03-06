<script>
export default {
    name: 'ActionHelper',
    methods: {
        actionChangePipeline(value) {
            return { pipeline_id: value }
        },
        actionChangeStage(value) {
            return { stage_id: value }
        },
        actionChangeTaskStage(value) {
            return { task_stage_id: value }
        },
        actionChangeResponsible(value) {
            return { responsible_id: value }
        },
        actionChangeManager(value) {
            return { manager_id: value }
        },
        actionChangeExecutor(value) {
            return { executor_id: value }
        },
        actionChangeStartTime(value) {
            return { start_time: value }
        },
        actionChangeEndTime(value) {
            return { end_time: value }
        },
        actionFormData(action) {
            let formData = new FormData();
            if (!!action) {
                if (!!this.action.pipeline_id) {
                    formData.append('action[change_pipeline]', this.action.pipeline_id ?? null);
                }

                if (!!this.action.stage_id) {
                    formData.append('action[change_stage]', this.action.stage_id ?? null);
                }

                if (!!action.task_stage_id) {
                    formData.append('action[change_task_stage]', action.task_stage_id ?? null);
                }

                if (!!action.manager_id) {
                    formData.append('action[change_manager]', action.manager_id ?? null);
                }

                if (!!action.executor_id) {
                    formData.append('action[change_executor]', action.executor_id ?? null);
                }

                if (!!action.responsible_id) {
                    formData.append('action[change_responsible]', action.responsible_id ?? null);
                }

                if (!!action.comment) {
                    formData.append('action[comment]', action.comment ?? false);
                }

                if (!!action.start_time) {
                    formData.append('action[change_start_time]', action.start_time ?? false);
                }

                if (!!action.end_time) {
                    formData.append('action[change_end_time]', action.end_time ?? false);
                }
            }

            return formData;
        }
    }
}
</script>
