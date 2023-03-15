<?php
    use App\Models\Pipeline;
    $pipelines = Pipeline::query()->get();
    $default = $pipelines->first()->id;
    $pipeline_id = $crud->getRequest()->get('pipeline', $default);
?>
<div style="display: flex; gap: 10px; margin: 10px 0 10px 0;">
    @foreach($pipelines as $pipeline)
        <a href="/admin/deal?pipeline={{ $pipeline->id }}" class="btn btn-outline-primary {{$pipeline_id == $pipeline->id ? 'active' : ''}}">
            <span class="ladda-label"><i class="la la-eye"></i> {{$pipeline->name}} </span>
        </a>
    @endforeach
</div>
