@php
    use App\Services\Field\FieldService;
    use App\Models\Field;

    $service = new FieldService();
    if (empty($entry)) {
        $entry = new \App\Models\Client();
    }

    $fields = $service->getClientFields($entry);
    $included_fields = Field::includedClient()->get();
@endphp

@vite('resources/js/app.js')

@include('crud::fields.inc.wrapper_start')
<div id="vue-app">
    <client-edit
        :entry="{{json_encode($entry)}}"
        :fields="{{json_encode($fields)}}"
        :user="{{backpack_user()}}">
    </client-edit>
</div>
@include('crud::fields.inc.wrapper_end')

