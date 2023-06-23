<?php
    use App\Models\Pipeline;
    $pipelines = Pipeline::query()->get();
    $default = $pipelines->first()->id;
    $pipeline_id = $crud->getRequest()->get('pipeline');
?>
<div style="display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0 10px 0;">
    @foreach($pipelines as $pipeline)
        <a href="/admin/deal?pipeline={{ $pipeline->id }}" style="max-width: 32%; min-width: 32%" class="btn btn-outline-primary {{$pipeline_id == $pipeline->id ? 'active' : ''}}">
            <span class="ladda-label"><i class="la la-eye"></i> {{$pipeline->name}} </span>
        </a>
    @endforeach
</div>
