@if ($crud->hasAccess('dealCreate'))
    <a href="{{ url($crud->route.'/'.$entry->getKey().'/deal-create') }}" class="btn btn-sm btn-link"><i class="fa fa-list"></i>Создать сделку</a>
@endif
