<li class="nav-item"><a class="nav-link" href="{{ backpack_url('deal') }}"><i class="nav-icon la la-home"></i> {{ __('sidebar.deals') }} </a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> {{ __('sidebar.users') }} </a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('client') }}"><i class="nav-icon la la-users"></i> {{ __('sidebar.clients') }}</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">{{ __('sidebar.settings') }}</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('field') }}"><i class="nav-icon la la-edit"></i> {{ __('sidebar.fields') }} </a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('space') }}"><i class="nav-icon la la-city"></i> {{ __('sidebar.spaces') }} </a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('pipeline') }}"><i class="nav-icon la la-book"></i> {{ __('sidebar.pipelines') }} </a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('button') }}"><i class="nav-icon la la-icons"></i> {{  __('sidebar.buttons') }}</a></li>
    </ul>
</li>
