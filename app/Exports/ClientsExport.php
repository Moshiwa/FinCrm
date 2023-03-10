<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromView, WithStyles
{
    private Collection $clients;
    public function __construct($start, $end)
    {
        $this->clients = Client::query()
            ->with(['fields', 'deals'])
            ->when($start, function ($query, $start) {
                $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query, $end) {
                $query->where('created_at', '<=', $end);
            })
            ->get();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            "A1:E2" => [
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function view(): View
    {
        return view('exports.clients', [
            'clients' => $this->clients
        ]);
    }
}
