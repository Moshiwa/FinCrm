{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('deal') }}"><i class="nav-icon la la-home"></i> Deals</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">Entities</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-group"></i> Users</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('fields') }}"><i class="nav-icon la la-book"></i> Fields</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('pipelines') }}"><i class="nav-icon la la-book"></i> Pipelines</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('space') }}"><i class="nav-icon la la-question"></i> Spaces</a></li>
    </ul>
</li>
