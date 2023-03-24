<li class="nav-item"><a class="nav-link" href="{{ backpack_url('deal') }}"><i class="nav-icon la la-home"></i> {{ __('sidebar.deals') }} </a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('task') }}"><i class="nav-icon la la-tasks"></i> {{ __('sidebar.tasks') }} </a></li>

@role('admin')
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> {{ __('sidebar.users') }} </a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('manager') }}"><i class="nav-icon la la-user-friends"></i> {{ __('sidebar.managers') }}</a></li>
@endrole
@can('clients.list')
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('client') }}"><i class="nav-icon la la-users"></i> {{ __('sidebar.clients') }}</a></li>
@endcan

@role('admin')
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('report') }}"><i class="nav-icon la la-archive"></i> {{ __('sidebar.reports') }} </a></li>
@endrole
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('integration') }}"><i class="nav-icon la la-code-branch"></i> {{ __('sidebar.integrations') }} </a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">{{ __('sidebar.settings') }}</a>
    <ul class="nav-dropdown-items">
        @can('fields.list')
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('field') }}"><i class="nav-icon la la-edit"></i> {{ __('sidebar.fields') }} </a></li>
        @endcan
        @role('admin')
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('space') }}"><i class="nav-icon la la-city"></i> {{ __('sidebar.spaces') }} </a></li>
        @endrole
        @can('pipelines.list')
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('pipeline') }}"><i class="nav-icon la la-book"></i> {{ __('sidebar.pipelines') }} </a></li>
        @endcan
        @can('task_stages.list')
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('task-stage') }}"><i class="nav-icon la la-book"></i> {{ __('sidebar.task_stages') }}</a></li>
        @endcan
        @can('deal_buttons.list')
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('deal-button') }}"><i class="nav-icon la la-icons"></i> {{  __('sidebar.deal_buttons') }}</a></li>
        @endcan
        @can('task_buttons.list')
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('task-button') }}"><i class="nav-icon la la-icons"></i> {{ __('sidebar.task_buttons') }} </a></li>
        @endcan
    </ul>
</li>
