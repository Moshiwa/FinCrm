<?php

namespace App\Services\Report;

use App\Exports\ClientsExport;
use App\Exports\DealsExport;
use App\Exports\UsersExport;

class ReportService
{
    public static function factory($type, $data)
    {
        switch ($type) {
            case 'user_report':
                return new UsersExport($data['start'] ?? null, $data['end'] ?? null);
            case 'deal_report':
                return new DealsExport($data['start'] ?? null, $data['end'] ?? null);
            case 'client_report':
                return new ClientsExport($data['start'] ?? null, $data['end'] ?? null);
        }

        return null;
    }
}
