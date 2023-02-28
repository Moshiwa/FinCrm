<?php
    use App\Enums\FieldsEntitiesEnum;
    $request_entity = $crud->getRequest()->get('entity');
?>

<a href="/admin/field?entity={{ FieldsEntitiesEnum::deal->value }}" class="btn btn-outline-primary {{$request_entity === 'deal' ? 'active' : ''}}">
    <span class="ladda-label"><i class="la la-eye"></i> Показать поля сделок</span>
</a>
<a href="/admin/field?entity={{ FieldsEntitiesEnum::client->value }}" class="btn btn-outline-primary {{$request_entity === 'client' ? 'active' : ''}}">
    <span class="ladda-label"><i class="la la-eye"></i> Показать поля клиентов</span>
</a>
