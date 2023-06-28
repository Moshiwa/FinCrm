
@vite('resources/js/app.js')

@include('crud::fields.inc.wrapper_start')
<div id="vue-app--token-generate">
    <detail-user-token-generate
        :user="{{backpack_user()}}"
    />
</div>
@include('crud::fields.inc.wrapper_end')
