<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SpaceRequest;
use App\Models\Space;
use App\Services\Space\SpaceService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Exceptions\AccessDeniedException;

/**
 * Class SpaceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SpaceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Space::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/space');
        CRUD::setEntityNameStrings(__('entity.crud_titles.action.space'), __('entity.crud_titles.many.space'));
        if (! backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['list', 'update', 'delete', 'show']);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Наименование');
        CRUD::column('code')->label('Код');
        CRUD::column('active')->label('Активность')->type('boolean');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SpaceRequest::class);

        CRUD::field('active')->label('Активность');
        CRUD::field('name')->label('Наименование');
        CRUD::field('code')->label('Код');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        $entry = CRUD::getCurrentEntry();
        if($entry->code == SpaceService::$default_space_code) {
            CRUD::field('active')->attributes([
                'disabled' => 'disabled'
            ]);
        }


        CRUD::field('code')->type('hidden');
        CRUD::field('code2')->label('Код')->attributes([
            'disabled' => 'disabled'
        ])->value($entry->code);
    }

    public function spaceChange($code) {
        SpaceService::setCurrentSpaceCode($code);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return string
     */
    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        $id = $this->crud->getCurrentEntryId() ?? $id;

        if(Space::query()->count() <= 1) {
            return \Alert::add('error', 'Нельзя удалить последнюю организацию')->flash();
        }
        return $this->crud->delete($id);
    }
}
