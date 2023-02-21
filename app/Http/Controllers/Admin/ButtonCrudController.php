<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\ButtonOperation;
use App\Http\Requests\ButtonRequest;
use App\Models\Button;
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

        $options = [
            'display' => [
                'stages' => Arr::flatten($data['options']['display']['stages']) ?? []
            ],
            'stage_id' => $data['options']['stage_id'] ?? '',
            'pipeline_id' => $data['options']['pipeline_id'] ?? '',
            'responsible_id' => $data['options']['responsible_id'] ?? '',
        ];

        if (empty($data['id'])) {
            $button->create([
                'name' => $data['name'],
                'pipeline_id' => $data['pipeline_id'],
                'options' => $options
            ]);
        } else {
            $button = $button->find($data['id']);
            $button->update([
                'name' => $data['name'],
                'pipeline_id' => $data['pipeline_id'],
                'options' => $options
            ]);
        }
    }

}
