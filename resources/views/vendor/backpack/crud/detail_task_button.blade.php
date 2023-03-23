@extends(backpack_view('blank'))

@php
    use \App\Models\TaskStage;
    use \App\Services\Button\ButtonService;
    use \App\Models\User;
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      trans('backpack::crud.add') => false,
    ];
    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
    $crud = [];
    $service = new ButtonService();
    $buttons = \App\Models\TaskButton::query()->with(['visible', 'action'])->get();
    $buttons = $service->mergeTaskButtonsSettings($buttons);
    $task_stages = TaskStage::query()->select(['id', 'name'])->get();
    /*$task_stages = TaskStage::query()->select(['id', 'name'])->get();
    $ready_task_stages = [];
    foreach ($task_stages as $task_stage) {
        $ready_task_stages[] = $service->mergeTaskButtonsSettings($task_stage);
    }*/
    $users = User::query()->select(['id', 'name'])->get();
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
            <detail-task-button
                :auth="{{ backpack_user() }}"
                :buttons="{{ json_encode($buttons) }}"
                :task-stages="{{ $task_stages }}"
                :users="{{ $users }}"
            />
        </div>
    </div>

@endsection
