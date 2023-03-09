@if (backpack_user()->can('deals.create'))
<a href="{{ url('/admin/deal/client/'.$entry->getKey()) }}" class="btn btn-sm btn-link">
    <i class="fa fa-list"></i>
    Создать сделку
</a>
@endif
