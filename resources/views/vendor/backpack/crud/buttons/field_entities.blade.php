<?php
    use App\Enums\FieldsEntitiesEnum;
    $request_entity = $crud->getRequest()->get('entity', 'deal');
?>
<div style="display: flex; gap: 10px; margin: 10px 0 10px 0;">
    <a href="/admin/field?entity={{ FieldsEntitiesEnum::deal->value }}" class="btn btn-outline-primary {{$request_entity === 'deal' ? 'active' : ''}}">
        <span class="ladda-label"><i class="la la-eye"></i> Показать поля сделок</span>
    </a>
    <a href="/admin/field?entity={{ FieldsEntitiesEnum::client->value }}" class="btn btn-outline-primary {{$request_entity === 'client' ? 'active' : ''}}">
        <span class="ladda-label"><i class="la la-eye"></i> Показать поля клиентов</span>
    </a>
    <a href="/admin/field?entity={{ FieldsEntitiesEnum::task->value }}" class="btn btn-outline-primary {{$request_entity === 'task' ? 'active' : ''}}">
        <span class="ladda-label"><i class="la la-eye"></i> Показать поля задач</span>
    </a>
</div>
