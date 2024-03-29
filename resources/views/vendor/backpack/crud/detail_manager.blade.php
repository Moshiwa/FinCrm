@extends(backpack_view('blank'))

@php
    use \App\Models\Pipeline;
    use \App\Services\Button\ButtonService;

    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      trans('backpack::crud.add') => false,
    ];

    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

    $request = $crud->getRequest();
    $type = $request->get('type');
    $sort = $request->get('date_sort', 'desc');
    $start = $request->get('start');
    $end = $request->get('end');

    $user->load([
        'comments' => function ($query) use ($type, $sort, $start, $end) {
            $query->when($type, function ($query, $type) {
                 $query->where('type', $type);
            })
             ->when($start, function ($query, $start) {
                        $query->where('created_at', '>=', \Carbon\Carbon::createFromTimestamp($start));
                    })
            ->when($end, function ($query, $end) {
                $query->where('created_at', '<=', \Carbon\Carbon::createFromTimestamp($end));
            })
            ->offset(0)->limit(10)->orderBy('created_at', $sort);
        },
        'comments.files',
    ]);

    $filter = [];
    if($type) {
        $filter['type'] = $type;
    }
    if($sort) {
        $filter['sort'] = $sort;
    }

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
                :filter="{{ json_encode($filter) }}"
            />
        </div>
    </div>

@endsection
