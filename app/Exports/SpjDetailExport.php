<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class SpjDetailExport extends BaseExport implements FromArray, WithHeadings, WithCustomCsvSettings, WithColumnFormatting
{
    protected $spj;

    public function __construct($spj)
    {
        $this->spj = $spj;
    }

    public function array(): array
    {
        $spj = $this->spj;
        $spjDate = $spj->spj_date ? \Carbon\Carbon::parse($spj->spj_date)->format('d/m/Y') : '-';
        return [[
            $spj->id,
            $spj->nama_pegawai ?? '-',
            $spjDate,
            ucfirst($spj->status ?? '-'),
            $spj->keperluan ?? '-',
            $spj->bast_mutasi ?? '-',
            $spj->bast_inventaris ?? '-',
            $spj->penugasan_by ?? '-',
            $spj->perjalanan_from ?? '-',
            $spj->perjalanan_to ?? '-',
            $spj->transportasi ?? '-',
            $spj->biaya_estimasi ? (float) $spj->biaya_estimasi : 0.0,
        ]];
    }

    public function headings(): array
    {
        return [
            'ID','Nama Pegawai','SPJ Date','Status','Keperluan','BAST Mutasi','BAST Inventaris','Penugasan By','Perjalanan From','Perjalanan To','Transportasi','Biaya Estimasi'
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'line_ending' => "\r\n",
            'use_bom' => true,
        ];
    }

    public function columnFormats(): array
    {
        // L is the 'Biaya Estimasi' column
        return [
            'L' => '"Rp " #,##0',
        ];
    }
}
