<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ButtonRequest;
use App\Models\DealButton;
use App\Models\Pipeline;
use App\Services\Button\ButtonService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Arr;

class DealButtonCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\DealButton::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/button');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.field'), __('entity.crud_titles.many.field'));
        if (! backpack_user()->can('deal_buttons.list')) {
            CRUD::denyAccess(['list']);
        }
    }

    public function index()
    {
        $this->crud->hasAccessOrFail('list');

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural);

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::detail_deal_button', $this->data);
    }

    public function save(ButtonRequest $request)
    {
        $data = $request->validated();

        $button = DealButton::query();
        $options = [];

        if (empty($data['id'])) {
            if (! backpack_user()->can('deal_buttons.create')) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав'
                ], 403);
            }

            $button = $button->create([
                'name' => $data['name'],
                'pipeline_id' => $data['pipeline_id'],
                'color' => $data['color'] ?? null,
                'icon' => $data['icon'] ?? null,
                'options' => $options
            ]);
        } else {
            if (! backpack_user()->can('deal_buttons.update')) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас недостаточно прав'
                ], 403);
            }

            $button = $button->find($data['id']);
            $button->update([
                'name' => $data['name'],
                'pipeline_id' => $data['pipeline_id'],
                'color' => $data['color'] ?? null,
                'icon' => $data['icon'] ?? null,
                'options' => $options
            ]);
        }

        $stages = Arr::pluck($data['visible'], 'id');
        $stages = array_filter($stages);
        $button->visible()->sync($stages);
        $button->action()->update([
            'pipeline_id' => $data['action']['pipeline_id'] ?? null,
            'stage_id' => $data['action']['stage_id'] ?? null,
            'responsible_id' => $data['action']['responsible_id'] ?? null,
            'comment' => $data['action']['comment'] ?? false,
            'deadline_value' => $data['action']['deadline_value'] ?? null,
            'deadline_format_id' => $data['action']['deadline_format_id'] ?? null,
        ]);

        $pipeline = Pipeline::query()->find($data['pipeline_id']);
        $pipeline = (new ButtonService)->mergeDealButtonsSettings($pipeline);

        return response()->json([
            'success' => true,
            'data' => [
                'pipeline' => $pipeline
            ],
        ]);
    }

    public function delete(DealButton $button)
    {
        if (! backpack_user()->can('deal_buttons.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'У вас недостаточно прав'
            ], 403);
        }

        $button->delete();

        return response()->json([
            'success' => true,
        ]);
    }

}
