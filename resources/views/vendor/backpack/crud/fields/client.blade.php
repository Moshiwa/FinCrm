@php
    use App\Services\Field\FieldService;
    use App\Models\Field;
    use App\Models\Client;

    $service = new FieldService();
    if (empty($entry)) {
        $entry = new Client();
    }


    $included_fields = Field::includedClient()->get();
@endphp

@vite('resources/js/app.js')

@include('crud::fields.inc.wrapper_start')
<div id="vue-app">
    <detail-client
        :entry="{{json_encode($entry)}}"
        :fields="{{json_encode($entry->all_fields)}}"
        :user="{{backpack_user()}}"
    />
</div>
@include('crud::fields.inc.wrapper_end')

