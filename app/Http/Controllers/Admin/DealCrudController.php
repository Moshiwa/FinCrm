<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DealRequest;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Services\Deal\DealService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class DealCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DealCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \App\Http\Controllers\Admin\Operations\DealOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Deal::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/deal');
        CRUD::setEntityNameStrings('deal', 'deals');}

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('pipeline_id');
        CRUD::column('stage_id');
        CRUD::column('created_at');

        $request_entity = $this->crud->getRequest()->get('client');
        if ($request_entity) {
            $this->crud->addClause('where', 'client_id', $request_entity);
        }


        $pipelines = Pipeline::query()->select('id', 'name')->get()->toArray();
        $pipelines = Arr::pluck($pipelines, 'name', 'id');
        CRUD::addFilter([
            'type'  => 'dropdown',
            'name'  => 'pipeline',
            'label' => 'Воронка'
        ], $pipelines, function($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'pipeline_id', $value);
        });

        $stages = Stage::query()->select('id', 'name')->get()->toArray();
        $stages = Arr::pluck($stages, 'name', 'id');
        CRUD::addFilter([
            'type'  => 'dropdown',
            'name'  => 'stage',
            'label' => 'Стадия'
        ], $stages, function($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'stage_id', $value);
        });
    }

    public function update(DealRequest $request)
    {
        $service = new DealService();
        $data = $request->validated();

        $deal = Deal::query()->find($data['id']);

        $comment_data = $service->prepareCommentData($deal, $data);

        $deal->name = $data['name'];
        $deal->pipeline_id = $data['pipeline_id'];
        $deal->client_id = $data['client_id'];
        $deal->stage_id = $data['stage_id'];
        $deal->responsible_id = $data['responsible_id'];
        $deal->save();

        $service->createNewMessage($deal, $comment_data);
        $deal->fields()->sync($data['fields'] ?? []);
        $service->updateClient($deal, $data);
        $service->updateComments($deal, $data);

        $comment_count = $data['comment_count'] ?? 10;

        $deal->load([
            'stage',
            'pipeline',
            'pipeline.buttons',
            'pipeline.buttons.visible',
            'pipeline.buttons.action',
            'responsible'=> function ($query) {
                $query->select('id', 'name');
            },
            'client',
            'fields',
            'client.fields',
            'comments' => function ($query) use ($comment_count) {
                $query->orderBy('created_at', 'desc')->offset(0)->limit($comment_count);
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return response()->json([
            'deal' => $deal,
            'stages' => $deal->pipeline->stages,
            'pipelines' => Pipeline::query()->select(['id', 'name'])->get(),
        ]);

    }

    public function getStagesByPipeline(Pipeline $pipeline)
    {
        return $pipeline->stages()->select('id', 'name')->get();
    }

    public function loadComments(Deal $deal, Request $request)
    {
        $offset = $request->get('offset');
        $deal->load([
            'comments' => function ($query) use ($offset) {
                $query->offset($offset)->limit(5)->orderBy('created_at', 'desc');
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return $deal;
    }

    public function dealCreate()
    {
        $pipeline = Pipeline::query()->select('id', 'name')->first();
        $stage = Stage::query()->where('pipeline_id', $pipeline->id)->first();

        $client_id = $this->crud->getCurrentEntryId();
        $client = Client::query()->find($client_id);
        $deal = $client->deals()->create([
            'name' => 'Новая сделка',
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
            'responsible_id' => backpack_user()->id,
        ]);

        return redirect('/admin/deal/' . $deal->id . '/detail');
    }

}
