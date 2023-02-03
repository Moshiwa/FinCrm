<?php

namespace App\Http\Controllers;



use App\Models\Deal;
use Illuminate\Http\Request;

class FieldsController extends Controller
{
    public function saveField(Request $request)
    {
        $entity_id = $request->get('id');
        $entity = $request->get('entity');
        $field = $request->get('field');
        $model = $this->getModel($entity, $entity_id);
        $m = Deal::query()->find(1);
        dd($m->client());
        dd($request->get('id'));
    }

    private function getModel(string $entity, $entity_id)
    {
        $entities = explode('.', $entity);
        $model = $entities[0];
        $model = ucfirst($model);
        $model = "App\Models\\$model";
        return $model::query()->find($entity_id);
    }
}
