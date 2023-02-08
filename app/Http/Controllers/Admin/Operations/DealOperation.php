<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Field;
use App\Models\Pipeline;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

trait DealOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupDealRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/deal', [
            'as'        => $routeName.'.deal',
            'uses'      => $controller.'@getDealForm',
            'operation' => 'deal',
        ]);

        Route::post($segment . '/deal/save/{id}', [
            'as'        => $routeName . '.deal-send',
            'uses'      => $controller . '@postDealForm',
            'operation' => 'deal',
        ]);
    }

    public function getDealForm()
    {
        $entry = $this->crud->getCurrentEntry();
        $this->crud->hasAccessOrFail('deal');
        $this->crud->setHeading('Сделка');

        $entry->load([
            'stage',
            'pipeline',
            'responsible',
            'client',
            'fields',
            'client.fields',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        $this->data['crud'] = $this->crud;
        $this->data['entry'] = $entry;
        $this->data['pipelines'] = Pipeline::query()->select('id', 'name')->get();
        $this->data['fields'] = Field::query()->get();
        $this->data['saveAction'] = $this->crud->getSaveAction();

        return view('crud::deal', $this->data);
    }

    public function postDealForm()
    {
        $this->crud->hasAccessOrFail('deal');
        $request = $this->crud->validateRequest();

        $entry = $this->crud->getCurrentEntry();
        try {
            dd($request);
            // send the actual email
            Mail::raw($request['message'], function ($message) use ($entry, $request) {
                $message->from($request->from);
                $message->replyTo($request->reply_to);
                $message->to($entry->email, $entry->name);
                $message->subject($request['subject']);
            });

            Alert::success('Mail Sent')->flash();

            return redirect(url($this->crud->route));
        } catch (Exception $e) {
            // show a bubble with the error message
            Alert::error("Error, " . $e->getMessage())->flash();

            return redirect()->back()->withInput();
        }
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupDealDefaults()
    {
        CRUD::allowAccess(['deal']);

        CRUD::operation('deal', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            CRUD::addButton('line', 'deal', 'view', 'crud::buttons.deal');
            // CRUD::addButton('line', 'deal', 'view', 'crud::buttons.deal');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function deal()
    {
        CRUD::hasAccessOrFail('deal');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = CRUD::getTitle() ?? 'Deal '.$this->crud->entity_name;

        // load the view
        return view('crud::operations.deal', $this->data);
    }
}
