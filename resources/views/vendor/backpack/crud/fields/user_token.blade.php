@php
use App\Services\Field\FieldService;
use App\Models\Field;
use App\Models\Client;

@endphp
@vite('resources/js/app.js')

@include('crud::fields.inc.wrapper_start')
<div id="vue-app">
    <detail-user-token-generate
        :user="{{backpack_user()}}"
    />
</div>
@include('crud::fields.inc.wrapper_end')
