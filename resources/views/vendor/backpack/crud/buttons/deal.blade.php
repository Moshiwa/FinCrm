@if ($crud->hasAccess('deal'))
  <a href="{{ url($crud->route.'/'.$entry->getKey().'/deal') }}" class="btn btn-sm btn-link text-capitalize"><i class="la la-question"></i> deal</a>
@endif
