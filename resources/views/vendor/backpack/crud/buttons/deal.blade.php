@if ($crud->hasAccess('deal'))
  <a href="{{ url($crud->route.'/'.$entry->getKey()) }}" class="btn btn-sm btn-link text-capitalize"><i class="la la-eye"></i> Просмотр</a>
@endif
