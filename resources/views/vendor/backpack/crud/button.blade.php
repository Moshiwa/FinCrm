@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
    $crud = [];

    $service = new \App\Services\Button\ButtonService();
    $pipelines = \App\Models\Pipeline::query()->with([
        'stages',
        'buttons',
        'buttons.visible',
        'buttons.action',
    ])->get();

    $pipelines = $service->mergeButtonsSettings($pipelines);

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
                :pipelines="{{ json_encode($pipelines) }}"
            />
        </div>
    </div>

@endsection
