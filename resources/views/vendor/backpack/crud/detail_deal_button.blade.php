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
    $pipelines = Pipeline::query()->select('id', 'name')->get();

    $ready_pipelines = [];
    foreach ($pipelines as $pipeline) {
        $ready_pipelines[] = $service->mergeDealButtonsSettings($pipeline);
    }

@endphp


@section('content')
    @vite('resources/js/app.js')

    <div>
        <div id="vue-app">
            <section class="container-fluid">
                <h2>
                    <span class="text-capitalize">Кнопки</span>
                    <small>Настройки</small>
                </h2>
            </section>
            <detail-deal-button
                :pipelines="{{ json_encode($ready_pipelines) }}"
            />
        </div>
    </div>

@endsection
