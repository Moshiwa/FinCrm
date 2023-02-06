{{-- This file is used to store topbar (right) items --}}

@php
    use \App\Services\Space\SpaceService;
    use Illuminate\Support\Facades\Auth;

    $currentSpace = \App\Models\Space::query()->where('enable', true)->first();
    $spaces = Auth::user()->availableSpaces();
@endphp

<li class="nav-item dropdown pr-4">
    <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        <i class="la la-globe"></i>
        {{ $currentSpace->name}}
    </a>
    @if(count($spaces) > 1 || (count($spaces) == 1 && $spaces[0]->code != $currentSpace->code))
        <div
            class="dropdown-menu {{ config('backpack.base.html_direction') == 'rtl' ? 'dropdown-menu-left' : 'dropdown-menu-right' }} mr-4 pb-1 pt-1">
            <h6 class="dropdown-header">Выберите организацию</h6>
            @foreach($spaces as $space)
                @if($space->code == $currentSpace->code)
                    <span class="dropdown-item disabled">
                        <i class="la la-globe"></i> {{ $space->name  }}
                    </span>
                @else
                    <a class="dropdown-item" href="{{ route('space.change', ['code' => $space->code]) }}">
                        <i class="la la-globe"></i> {{ $space->name  }}
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</li>

{{-- <li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-bell"></i><span class="badge badge-pill badge-danger">5</span></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-list"></i></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-map"></i></a></li> --}}
