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


@endphp


@section('content')
    @vite('resources/js/app.js')

    <div>
        <div id="vue-app">
            <section class="container-fluid">
                <h2>
                    <span class="text-capitalize">Отчеты</span>
                </h2>
            </section>
            <detail-report/>
        </div>
    </div>

@endsection
