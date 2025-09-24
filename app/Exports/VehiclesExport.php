<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class VehiclesExport extends BaseExport implements FromCollection, WithHeadings, WithCustomCsvSettings, WithColumnFormatting
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Asset::where('tipe','Kendaraan');

        // Apply filters
        if (!empty($this->filters)) {
            if (isset($this->filters['pic'])) $query->where('pic', 'like', '%'.$this->filters['pic'].'%');
            if (isset($this->filters['project'])) $query->where('project', $this->filters['project']);
            if (isset($this->filters['lokasi'])) $query->where('lokasi', $this->filters['lokasi']);

            // Apply sorting if provided
            if (isset($this->filters['sort'])) {
                $direction = isset($this->filters['dir']) ? $this->filters['dir'] : 'asc';
                $query->orderBy($this->filters['sort'], $direction);
            }
        }

        return $query->get()->map(function ($asset) {
            return [
                $asset->jenis_aset,
                $asset->pic,
                $asset->merk,
                $asset->tanggal_beli ? $asset->tanggal_beli : '',
                $asset->serial_number ?? '',
                $asset->project,
                $asset->lokasi,
                $asset->harga_sewa ? (float) $asset->harga_sewa : 0.0,
                $asset->status_pajak ?? '',
                $asset->total_servis ? (float) $asset->total_servis : 0.0,
            ];
        });
    }

    public function headings(): array
    {
    return [
            'Jenis Aset','PIC','Merk','Tanggal Beli','Nomor Aset','Project',
            'Lokasi','Harga Sewa','Status Pajak','Total Servis'
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
        // H = Harga Sewa, J = Total Servis (1-based columns, adjust to actual columns if headings change)
        return [
            'H' => '"Rp " #,##0',
            'J' => '"Rp " #,##0',
        ];
    }
}
