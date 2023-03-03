@extends(backpack_view('blank'))

@php
    use App\Services\Field\FieldService;
    use App\Models\User;

    $defaultBreadcrumbs = [];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

    $service = new FieldService();

    $deal_fields = $service->getDealFields($entry);
    $client_fields = $service->getClientFields($entry->client);

    $users = User::query()->where('id', $entry->responsible_id)->get();
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

        <detail-deal
            :auth="{{ backpack_user() }}"
            :deal="{{ $entry }}"
            :pipelines="{{ $pipelines }}"
            :stages="{{ $stages }}"
            :deal-fields="{{ json_encode($deal_fields) }}"
            :client-fields="{{ json_encode($client_fields) }}"
            :users="{{$users}}"
            :buttons="{{$buttons}}"
        />
    </div>
</div>

@endsection

