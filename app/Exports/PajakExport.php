<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Carbon\Carbon;

class PajakExport extends BaseExport implements FromArray, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithCustomCsvSettings
{
    protected $asset;

    public function __construct($asset)
    {
        $this->asset = $asset;
    }

    public function array(): array
    {
        return [[
            $this->asset->merk,
            $this->asset->tanggal_pajak ? Carbon::parse($this->asset->tanggal_pajak)->format('d M Y') : '',
            $this->asset->jumlah_pajak ? (float) $this->asset->jumlah_pajak : 0.0,
            $this->asset->status_pajak ?? '',
        ]];
    }

    public function headings(): array
    {
        return ['Merk','Tanggal Pajak','Jumlah Pajak','Status Pajak'];
    }

    public function columnFormats(): array
    {
        // D = Jumlah Pajak (format mata uang IDR)
        return [
            'C' => '"Rp "#,##0',
        ];
    }

        /**
         * CSV settings: semicolon delimiter with BOM
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
}
