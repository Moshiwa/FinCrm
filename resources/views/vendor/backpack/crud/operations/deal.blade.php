@extends(backpack_view('blank'))

@php
    use App\Services\Field\FieldService;
    use App\Models\User;
    use App\Models\Pipeline;
    use App\Models\DealButton;

    $defaultBreadcrumbs = [];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

    $service = new FieldService();

    $deal->load([
            'stage',
            'pipeline',
            'responsible',
            'client',
            'fields.type',
            'client.fields',
            'comments' => function ($query) {
                $query->offset(0)->limit(10)->orderBy('created_at', 'desc');
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

    $pipelines = Pipeline::query()->select('id', 'name')->get();
    $stages = $deal->pipeline->stages;
    $buttons = DealButton::query()->with(['visible', 'action'])->where('pipeline_id', $deal->pipeline->id)->get();

    $all_fields = \App\Models\Field::includedDeal()->get();
    foreach ($all_fields as $field) {
        foreach ($deal->fields as $filled_field) {
            if ($filled_field->id === $field->id) {
                continue(2);
            }
        }
        $deal->fields->push($field);
    }

    $all_fields = \App\Models\Field::includedClient()->get();
    foreach ($all_fields as $field) {
        foreach ($deal->client->fields as $filled_field) {
            if ($filled_field->id === $field->id) {
                continue(2);
            }
        }
        $deal->client->fields->push($field);
    }


    /*$deal_fields = $service->getDealFields($deal);
    $client_fields = $service->getClientFields($deal->client);*/

    $users = User::query()->select(['id', 'name'])->get();
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
            :deal="{{ $deal }}"
            :pipelines="{{ $pipelines }}"
            :stages="{{ $stages }}"

            :users="{{$users}}"
            :buttons="{{$buttons}}"
        />
    </div>
</div>

@endsection

