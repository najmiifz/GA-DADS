<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DynamicAssetExport extends BaseExport implements FromCollection, WithHeadings, WithCustomCsvSettings, WithColumnFormatting
{
    use Exportable;

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        // Export assets for custom pages matching the table columns
        return $this->query->get()->map(function ($asset) {
            // Determine display value for PIC
            $picName = $asset->pic ?? '';
            // Handle pattern user:{id}
            if (strpos($picName, 'user:') === 0) {
                $userId = substr($picName, 5);
                $user = \App\Models\User::find($userId);
                $picName = $user && $user->name ? $user->name : $picName;
            } elseif ($asset->user && $asset->user->name) {
                // Fallback to relation if available
                $picName = $asset->user->name;
            }
            return [
                $asset->tipe,
                $asset->jenis_aset,
                $asset->merk,
                $asset->project,
                $asset->lokasi,
                $asset->status_pajak ?? '',
                $asset->total_servis ? (float) $asset->total_servis : 0.0,
                $asset->harga_sewa ? (float) $asset->harga_sewa : 0.0,
                $picName,
                $asset->serial_number ?? '',
            ];
        });
    }

    public function headings(): array
    {
        // Headers matching asset-pages listing
        return [
            'Tipe',
            'Jenis Aset',
            'Merk',
            'Project',
            'Lokasi',
            'Status Pajak',
            'Total Servis',
            'Harga Sewa',
            'PIC',
            'Nomor Aset',
        ];
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

    public function columnFormats(): array
    {
        // G = Total Servis, H = Harga Sewa
        return [
            'G' => '"Rp " #,##0',
            'H' => '"Rp " #,##0',
        ];
    }
}
