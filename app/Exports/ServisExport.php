<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ServisExport extends BaseExport implements FromCollection, WithHeadings, WithCustomCsvSettings, WithColumnFormatting
{
    use Exportable;

    protected $rows;

    public function __construct($services)
    {
        $this->rows = $services;
    }

    public function collection()
    {
        return collect($this->rows)->map(function($s) {
            return [
                $s->service_date ? \Carbon\Carbon::parse($s->service_date)->format('d M Y') : '',
                $s->description,
                $s->vendor,
                $s->cost ? (float) $s->cost : 0.0,
            ];
        });
    }

    public function headings(): array
    {
        return ['Tanggal','Keterangan','Vendor','Biaya'];
    }

    /**
     * CSV settings: semicolon delimiter and BOM
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'line_ending' => "\r\n",
            'use_bom' => true,
        ];
    }

    /**
     * Column formatting for XLSX (applies to Excel, not CSV)
     */
    public function columnFormats(): array
    {
        // D is the 'Biaya' column
        return [
            'D' => '"Rp " #,##0',
        ];
    }

}
