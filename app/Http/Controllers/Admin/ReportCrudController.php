<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Admin\Operations\ReportOperation;
use App\Http\Requests\ReportRequest;
use App\Services\Report\ReportService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportCrudController extends CrudController
{
    use ReportOperation;

    public function setup()
    {
        CRUD::setRoute(config('backpack.base.route_prefix') . '/report');
        CRUD::setEntityNameStrings('report', 'reports');
    }

    public function reportGenerate(Request $request)
    {
        $type = $request->get('type');
        $start = $request->get('start', null);
        $end = $request->get('end', null);

        $data = [
            'start' => empty($start) ? $start : Carbon::createFromTimestamp($start),
            'end' => empty($end) ? $end : Carbon::createFromTimestamp($end)
        ];

        $export = ReportService::factory($type, $data);

        return Excel::download($export, 'users.xlsx');
    }
}
