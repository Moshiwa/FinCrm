@if (backpack_user()->can('tasks.create'))
    <a href="{{ url('/admin/task/new/') }}" class="btn btn-primary">
        <i class="fa fa-list"></i>
        Создать задачу
    </a>
@endif
