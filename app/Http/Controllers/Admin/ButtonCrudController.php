<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\ButtonOperation;
use App\Http\Requests\ButtonRequest;
use App\Models\Button;
use App\Models\Pipeline;
use App\Services\Button\ButtonService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Arr;

class ButtonCrudController extends CrudController
{
    use ButtonOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Button::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/button');
        CRUD::setEntityNameStrings('button', 'buttons');
    }

    public function save(ButtonRequest $request)
    {
        $data = $request->validated();

        $button = Button::query();
        $options = [];

        if (empty($data['id'])) {
            $button = $button->create([
                'name' => $data['name'],
                'pipeline_id' => $data['pipeline_id'],
                'color' => $data['color'] ?? null,
                'icon' => $data['icon'] ?? null,
                'options' => $options
            ]);
        } else {
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
        ]);

        $pipeline = Pipeline::query()->find($data['pipeline_id']);
        $pipeline = (new ButtonService)->mergeButtonsSettings($pipeline);

        return response()->json([
            'data' => [
                'pipeline' => $pipeline
            ],
            'errors' => [],
        ]);
    }

    public function delete(Button $button)
    {
        $button->delete();
    }

}
