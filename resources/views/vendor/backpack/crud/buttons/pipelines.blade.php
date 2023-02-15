<?php
    use App\Enums\FieldsEntitiesEnum;
?>

<a href="/admin/field?entity={{ FieldsEntitiesEnum::deal->value }}" class="btn btn-outline-primary">
    <span class="ladda-label"><i class="la la-eye"></i> Показать поля сделок</span>
</a>
<a href="/admin/field?entity={{ FieldsEntitiesEnum::client->value }}" class="btn btn-outline-primary">
    <span class="ladda-label"><i class="la la-eye"></i> Показать поля клиентов</span>
</a>
