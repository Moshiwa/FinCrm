@php
    use App\Models\User;
    if (empty($entry)) {
        $entry = new User();
    }
@endphp

@vite('resources/js/app.js')

@include('crud::fields.inc.wrapper_start')
<div id="vue-app">
    <telephony-auth-fields
        :entry="{{json_encode($entry)}}"
        :user="{{backpack_user()}}"
    />
</div>
@include('crud::fields.inc.wrapper_end')

