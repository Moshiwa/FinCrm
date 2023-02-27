@extends(backpack_view('blank'))

@php
    use \App\Models\Pipeline;
    use \App\Services\Button\ButtonService;

    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
    $crud = [];

    $service = new ButtonService();
    $pipelines = Pipeline::query()->get();

    $reade_pipelines = [];
    foreach ($pipelines as $pipeline) {
        $reade_pipelines[] = $service->mergeButtonsSettings($pipeline);
    }

@endphp


@section('content')
    @vite('resources/js/app.js')

    <div>
        <div id="vue-app">
            <section class="container-fluid">
                <h2>
                    <span class="text-capitalize">Настрйоки</span>
                    <small>Поднасрйки</small>
                </h2>
            </section>
            <button-settings
                :pipelines="{{ json_encode($reade_pipelines) }}"
            />
        </div>
    </div>

@endsection
