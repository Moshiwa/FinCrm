<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromView, WithStyles
{
    private Collection $users;
    public function __construct($start, $end)
    {
        $this->users = User::query()
            ->select(['id', 'name', 'email', 'created_at'])
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
            "A1:C2" => [
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
        return view('exports.users', [
            'users' => $this->users
        ]);
    }
}
