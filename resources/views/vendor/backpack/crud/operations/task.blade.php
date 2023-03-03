@extends(backpack_view('blank'))

@php
    use App\Services\Field\FieldService;
    use App\Models\User;

    $defaultBreadcrumbs = [];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

    $service = new FieldService();

    $task->load([
        'stage',
        'fields.type',
        'comments' => function ($query) {
            $query->offset(0)->limit(10)->orderBy('created_at', 'desc');
        },
        'comments.files',
        'comments.author' => function ($query) {
            $query->select('id', 'name');
        },
        'responsible',
        'manager',
        'executor',
    ]);

    $task_fields = $service->getTaskFields($task);
    $stages = \App\Models\TaskStage::query()->get();
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

            <detail-task
                :task="{{ $task }}"
                :auth="{{ backpack_user() }}"
                :stages="{{ $stages }}"
                :users="{{ $users }}"
                :task-fields="{{ json_encode($task_fields) }}"
            />
        </div>
    </div>

@endsection

