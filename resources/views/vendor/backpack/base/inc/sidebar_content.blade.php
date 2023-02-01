{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">Entities</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-group"></i> Users</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('fields') }}"><i class="nav-icon la la-book"></i> Fields</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('stages') }}"><i class="nav-icon la la-book"></i> Stages</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('pipelines') }}"><i class="nav-icon la la-book"></i> Pipelines</a></li>
    </ul>
</li>
