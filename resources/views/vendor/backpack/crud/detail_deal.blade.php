@extends(backpack_view('blank'))

@php
    use App\Services\Field\FieldService;
    use App\Models\User;
    use App\Models\Pipeline;
    use App\Models\DealButton;

    $defaultBreadcrumbs = [];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

    $service = new FieldService();

    $filter = [];
    $request = $crud->getRequest();
    $type = $request->get('type');
    $sort = $request->get('date_sort', 'desc');

    if($type) {
        $filter['type'] = $type;
    }

    if($sort) {
        $filter['sort'] = $sort;
    }

    $deal->load([
            'stage' => function ($query) {
                $query->orderBy('lft');
            },
            'pipeline',
            'responsible',
            'client',
            'fields.type',
            'comments' => function ($query) use ($type, $sort) {
                $query->when($type, function ($query, $type) {
                     $query->where('type', $type);
                })
                ->offset(0)->limit(10)->orderBy('created_at', $sort);
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

    $pipelines = Pipeline::query()->select('id', 'name')->get();
    $stages = $deal->pipeline->stages()->orderBy('lft')->get();
    $buttons = DealButton::query()->with(['visible', 'action'])->where('pipeline_id', $deal->pipeline->id)->get();
    $users = User::query()->select(['id', 'name'])->get();
@endphp

@section('content')
@vite('resources/js/app.js')

<div>
    @include('crud::inc.grouped_errors')
    <div id="vue-app">
        <section class="container-fluid">
            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </section>

        <detail-deal
            :auth="{{ backpack_user() }}"
            :deal="{{ $deal }}"
            :pipelines="{{ $pipelines }}"
            :stages="{{ $stages }}"
            :filter="{{ json_encode($filter) }}"
            :users="{{$users}}"
            :buttons="{{$buttons}}"
        />
    </div>
</div>

@endsection

