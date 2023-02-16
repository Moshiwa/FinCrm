@php
    use App\Services\Stage\StageService;
    use App\Models\Field;

    $service = new StageService();

    $settings = $service->getAllSettings($entry);

@endphp

@vite('resources/js/app.js')

@include('crud::fields.inc.wrapper_start')
<div id="vue-app">
    <stage-settings
        :entry="{{ $entry }}"
        :settings="{{ json_encode($settings) }}"
    />
</div>
@include('crud::fields.inc.wrapper_end')

