{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('deal') }}"><i class="nav-icon la la-home"></i> {{ __('sidebar.deals') }} </a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-group"></i> {{ __('sidebar.users') }} </a></li>


<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">{{ __('sidebar.entities') }}</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('pipeline') }}"><i class="nav-icon la la-book"></i> {{ __('sidebar.pipelines') }} </a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('space') }}"><i class="nav-icon la la-question"></i> {{ __('sidebar.spaces') }} </a></li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">{{ __('sidebar.directories') }}</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('field') }}"><i class="nav-icon la la-book"></i> {{ __('sidebar.fields') }} </a></li>
    </ul>
</li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('settings') }}"><i class="nav-icon la la-question"></i> Settings</a></li>