@extends(backpack_view('blank'))

@php
    use App\Services\Field\FieldService;
    use App\Models\User;
    use App\Models\Pipeline;
    use App\Models\DealButton;

    $defaultBreadcrumbs = [];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

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

            <detail-integration
                :integration="{{ $integration }}"
                :auth="{{ $user }}"
            />
        </div>
    </div>

@endsection

