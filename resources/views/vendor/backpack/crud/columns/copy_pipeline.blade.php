<?php
$spaces = \App\Models\Space::query()->get();
$currentSpace = \App\Services\Space\SpaceService::getCurrentSpace();
$pipeline_id = $entry->getKey();
?>
<div style="position: relative; width: fit-content">
    <a href="javascript:void(0)" onclick="openDialog('{{$pipeline_id}}')"
       id="copy-button-pipeline_{{$pipeline_id}}"
       class="btn btn-sm btn-link">
        <i class="la la-copy"></i>
        Копировать
    </a>
    <div id="dropdown-menu-copy-pipeline_{{$pipeline_id}}" class="dropdown-menu dropdown-menu-right pb-1 pt-1">
        <h6 class="dropdown-header">Выберите организацию</h6>
        @foreach($spaces as $space)
        <a class="dropdown-item" style="cursor: pointer" onclick="copyPipeline('{{$space->id}}', '{{$pipeline_id}}')">
            <i class="la la-globe"></i>
            {{ $space->name }}
        </a>
        @endforeach
    </div>
</div>
<script>
    var globalPipelineId = null;
    document.addEventListener( 'click', (e) => {
        let currentElement = e.target;
        let elems = document.getElementsByClassName("dropdown-menu");
        elems.forEach((elem, index) => {
            if(currentElement.id !== 'copy-button-pipeline_' + globalPipelineId) {
                elems[index].classList.remove('show');
            }
        });
    });

    function copyPipeline(spaceId, pipelineId) {
        fetch('/admin/pipeline/copy/space?pipeline_id=' + pipelineId + '&space_id=' + spaceId)
            .then(response => response.json())
            .then((result) => {
                window.location.reload();
            });
    }

    function openDialog(pipelineId)
    {
        let elem = document.getElementById("dropdown-menu-copy-pipeline_" + pipelineId);
        let elems = document.getElementsByClassName("dropdown-menu");
        elems.forEach((elem, index) => {
            if(elem.id !== 'copy-button-pipeline_' + pipelineId) {
                elems[index].classList.remove('show');
            }
        });
        elem.classList.add('show');
        globalPipelineId = pipelineId;
    }
</script>
<!--<ul class="nav navbar-nav ml-auto ">

    <li class="nav-item dropdown pr-4">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="la la-globe"></i>
            Копировать в
        </a>
        <div class="dropdown-menu dropdown-menu-right mr-4 pb-1 pt-1">
            <h6 class="dropdown-header">Выберите организацию</h6>
            <span class="dropdown-item disabled">
                        <i class="la la-globe"></i> Основное
                    </span>
            <a class="dropdown-item" href="http://localhost:8000/admin/space-change/test">
                <i class="la la-globe"></i> test
            </a>
        </div>
    </li>
</ul>
</a>-->
