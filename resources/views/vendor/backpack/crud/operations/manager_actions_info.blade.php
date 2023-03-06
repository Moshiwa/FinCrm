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
    $user->load([
        'deal_comments' => function ($query) {
            $query->offset(0)->limit(5)->orderBy('created_at', 'desc');
        },
        'deal_comments.files',
        'task_comments' => function ($query) {
            $query->offset(0)->limit(5)->orderBy('created_at', 'desc');
        },
        'task_comments.files'
    ]);

@endphp


@section('content')
    @vite('resources/js/app.js')

    <div>
        <div id="vue-app">
            <section class="container-fluid">
                <h2>
                    <span class="text-capitalize">Что делал</span>
                    <small>{{ $user->name }}</small>
                </h2>
            </section>
            <detail-manager-actions
                :user="{{ $user }}"
                :auth="{{ backpack_user() }}"
            />
        </div>
    </div>

@endsection
