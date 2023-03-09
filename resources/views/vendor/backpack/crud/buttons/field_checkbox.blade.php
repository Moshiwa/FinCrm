<input
    type="checkbox"
    data-id="{{ $entry->getKey() }}"
    onchange="toggleFieldActivity(this)"
    <?php if($entry->is_active) {echo 'checked';} ?>
>
<script>
    if (typeof toggleFieldActivity != 'function') {
        function toggleFieldActivity(checkbox) {
            let fieldId = checkbox.getAttribute('data-id');
            if (fieldId) {
                fetch('/admin/field/' + fieldId  + '/toggle-activity/?is_active=' + checkbox.checked, {
                    method: 'GET'
                });
            }
        }
    }
</script>
