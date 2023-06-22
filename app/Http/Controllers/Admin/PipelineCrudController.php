<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PipelineRequest;
use App\Models\DeadlineFormat;
use App\Models\Pipeline;
use App\Models\Space;
use App\Services\Space\SpaceService;
use App\Services\Stage\StageService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

class PipelineCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Pipeline::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/pipeline');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.pipeline'), __('entity.crud_titles.many.pipeline'));
        CRUD::denyAccess(['show']);
        if (! backpack_user()->can('pipelines.update')) {
            CRUD::denyAccess(['update']);
        }

        if (! backpack_user()->can('pipelines.create')) {
            CRUD::denyAccess(['create']);
        }

        if (! backpack_user()->can('pipelines.delete')) {
            CRUD::denyAccess(['delete']);
        }

        if (! backpack_user()->can('pipelines.list')) {
            CRUD::denyAccess(['list']);
        }
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->label('Наименование');
        if (backpack_user()->can('pipelines.create')) {
            CRUD::column('Кастомные действия')
                ->type('view')
                ->view('crud::columns.copy_pipeline');
        }
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PipelineRequest::class);

        CRUD::field('name')->label('Наименование');
        CRUD::field('stages')
            ->label('Стадии')
            ->reorder('lft')
            ->type('relationship')
            ->default(StageService::getDefaultStages())
            ->min_rows(1)
            ->subfields([
                [
                    'name' => 'name',
                    'label' => 'Наименование',
                    'type' => 'text',
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                ],
                [
                    'name' => 'deadline',
                    'label' => 'Срок для сделки',
                    'type' => 'number',
                    'wrapper' => [
                        'class' => 'form-group col-md-3',
                        'required' => true
                    ],
                ],
                [
                    'name' => 'deadline_format_id',
                    'entity' => 'deadline_format',
                    'label' => 'Формат',
                    'attribute' => 'tr_name',
                    'type' => 'relationship',
                    'model' => DeadlineFormat::class,
                    'allows_null' => false,
                    'wrapper' => [
                        'class' => 'form-group col-md-3',
                        'required' => true
                    ],
                ],
            ]
        );
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        try {
            // update the row in the db
            $item = $this->crud->update(
                $request->get($this->crud->model->getKeyName()),
                $this->crud->getStrippedSaveRequest($request)
            );
            $this->data['entry'] = $this->crud->entry = $item;

            // show a success message
            \Alert::success(trans('backpack::crud.update_success'))->flash();

            // save the redirect choice for next time
            $this->crud->setSaveAction();
        } catch (\Exception $e) {
            $code = $e->getCode();
            if ($code == 23503) {
                \Alert::add('error', 'Вы не можете удалить стадию у которой имееются сделки')->flash();
            } else {
                \Alert::add('error', 'Произошла ошибка')->flash();
            }

            $this->data['entry'] = $this->crud->model;
            $this->crud->setSaveAction();
            return $this->crud->performSaveAction($request->get($this->crud->model->getKeyName()));
        }

        return $this->crud->performSaveAction($item->getKey());
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        try {
            $this->crud->delete($id);
        } catch (\Exception $e) {
            $code = $e->getCode();
            if ($code == 23503) {
                return \Alert::add('error', 'Вы не можете удалить воронку у которой имееются сделки')->flash();
            }

            return \Alert::add('error', 'Произошла ошибка')->flash();
        }

        return true;
    }

    public function copyPipeline(Request $request)
    {
        if (! backpack_user()->can('pipelines.create')) {
            return response()->json([
                'success' => false,
                'message' => 'У вас недостаточно прав'
            ], 403);
        }

        $pipeline_id = $request->get('pipeline_id');
        $space_id = $request->get('space_id');

        $pipeline = Pipeline::query()->find($pipeline_id);
        $stages = $pipeline->stages;
        $space = Space::query()->find($space_id);

        $new_pipeline = $space->pipelines()->create([
            'name' => $pipeline->name . ' /Копия',
        ]);

        foreach ($stages as $stage) {
            $space->stages()->create([
                'name' => $stage->name,
                'deadline' => $stage->deadline,
                'deadline_format_id' => $stage->deadline_format_id,
                'pipeline_id' => $new_pipeline->id,
            ]);
        }

        $reload = false;
        $current_space = SpaceService::getCurrentSpace();
        if ($current_space->id === $space->id) {
            $reload = true;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'reload' => $reload
            ]
        ]);

    }
}
