@php
    use App\Services\Stage\StageService;
    use App\Models\Stage;

    $service = new StageService();

    $settings = $service->getAllSettings($entry);
    $stages = Stage::query()
        ->where('pipeline_id', $entry->pipeline_id)
        ->whereNot('id', $entry->id)
        ->get();

@endphp

@vite('resources/js/app.js')

@include('crud::fields.inc.wrapper_start')
<div id="vue-app">
    <stage-settings
        :entry="{{ $entry }}"
        :settings="{{ json_encode($settings) }}"
        :stages="{{ json_encode($stages) }}"
    />
</div>
@include('crud::fields.inc.wrapper_end')

