<?php

namespace App\Exports;

use App\Models\MaterialHistory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialHistoryExport implements FromView, WithStyles, ShouldAutoSize
{
    protected $materialId;
    protected $request;

    public function __construct($materialId, $request)
    {
        $this->materialId = $materialId;
        $this->request = $request;
    }

    public function view(): View
{
    $query = MaterialHistory::query()
        ->when($this->materialId, function ($q) {
            $q->where('material_id', $this->materialId);
        })
        ->when($this->request->tipe !== null && $this->request->tipe !== '', function ($q) {
            $q->where('tipe', $this->request->tipe);
        })
        ->when($this->request->search, function ($q) {
            $term = $this->request->search;

            $q->where(function ($sub) use ($term) {
                $sub->where('no_slip', 'ILIKE', "%{$term}%")
                    ->orWhere('catatan', 'ILIKE', "%{$term}%")
                    ->orWhereRaw("CAST(tanggal AS TEXT) ILIKE ?", ["%{$term}%"]);
            });
        })
        ->orderBy('tanggal', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    return view('exports.material-history', [
        'histories' => $query
    ]);
}


    /** 🎨 Styling Excel Sheet */
    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center'
            ],
        ]);

        // Border seluruh tabel
        $sheet->getStyle('A1:G' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '999999']
                    ]
                ]
            ]);

        // Rata tengah untuk kolom angka
        $sheet->getStyle('D:E')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B')->getAlignment()->setHorizontal('center');

        return [];
    }
}
