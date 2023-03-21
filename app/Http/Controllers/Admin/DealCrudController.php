<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DealRequest;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Pipeline;
use App\Models\Stage;
use App\Models\User;
use App\Services\Deal\DealService;
use App\Services\Field\FieldService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Deal::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/deal');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.deal'), __('entity.crud_titles.many.deal'));
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('pipeline_id');
        CRUD::column('stage_id');
        CRUD::column('created_at');

        $this->hiddenClientFilter();
        $this->hiddenResponsibleFilter();
        $this->hiddenPipelineFilter();

        CRUD::addButton('top', 'pipelines', 'view', 'crud::buttons.pipelines');
        $pipeline_id = CRUD::getRequest()->get('pipeline', Pipeline::query()->first()->id);
        $stages = Stage::query()->select('id', 'name')->where('pipeline_id', $pipeline_id)->get()->toArray();
        $stages = Arr::pluck($stages, 'name', 'id');
        CRUD::addFilter([
            'type'  => 'dropdown',
            'name'  => 'stage',
            'label' => 'Стадия'
        ], $stages, function($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'stage_id', $value);
        });
    }

    public function show($id)
    {
        $this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry (include softDeleted items if the trait is used)
        if ($this->crud->get('show.softDeletes') && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->crud->model))) {
            $this->data['entry'] = $this->crud->getModel()->withTrashed()->findOrFail($id);
        } else {
            $this->data['entry'] = $this->crud->getEntryWithLocale($id);
        }

        $this->data['crud'] = $this->crud;
        $this->data['deal'] = $this->data['entry'];
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.preview').' '.$this->crud->entity_name;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::detail_deal', $this->data);
    }

    private function hiddenClientFilter()
    {
        $request_entity = $this->crud->getRequest()->get('client');
        if ($request_entity) {
            $this->crud->addClause('where', 'client_id', $request_entity);
        }
    }

    private function hiddenResponsibleFilter()
    {
        $request_entity = $this->crud->getRequest()->get('responsible');
        if ($request_entity) {
            $this->crud->addClause('where', 'responsible_id', $request_entity);
        }
    }

    private function hiddenPipelineFilter()
    {
        $request_entity = $this->crud->getRequest()->get('pipeline');
        if ($request_entity) {
            $this->crud->addClause('where', 'pipeline_id', $request_entity);
        }
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
        $type = $request->get('type');
        $sort = $request->get('date_sort', 'desc');

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
            'comments' => function ($query) use ($type, $sort, $comment_count) {
                $query->when($type, function ($query, $type) {
                    $query->where('type', $type);
                })->offset(0)->limit($comment_count)->orderBy('created_at', $sort);
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return response()->json([
            'deal' => $deal,
            'stages' => $deal->pipeline->stages,
            'users' => User::query()->select(['id', 'name'])->get(),
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
        $type = $request->get('type');
        $sort = $request->get('date_sort', 'desc');
        $deal->load([
            'comments' => function ($query) use ($offset, $type, $sort) {
                $query
                    ->when($type, function ($query, $type) {
                        $query->where('type', $type);
                    })
                    ->offset($offset)
                    ->limit(5)
                    ->orderBy('created_at', $sort);
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

        return redirect('/admin/deal/' . $deal->id . '/show');
    }

    public function delete(Deal $deal, Request $request)
    {
        if (backpack_user()->can('deals.delete')) {
            $deal->delete();

            return response()->json([
                'success' => true,
                'errors' => [],
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => [
                'У вас недостаточно прав'
            ],
        ], 403);
    }

}
