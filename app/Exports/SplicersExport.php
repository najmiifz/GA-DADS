<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SplicersExport extends BaseExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Asset::where('tipe','Splicer');

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
                $asset->project,
                $asset->lokasi,
                $asset->harga_sewa ? (float) $asset->harga_sewa : 0.0,
                $asset->total_servis ? (float) $asset->total_servis : 0.0,
                $asset->serial_number ?? ''
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Jenis Aset','PIC','Merk','Project',
            'Lokasi','Harga Sewa','Total Servis','Nomor SN'
        ];
    }

    public function columnFormats(): array
    {
        // F = Harga Sewa, G = Total Servis
        return [
            'F' => '"Rp " #,##0',
            'G' => '"Rp " #,##0',
        ];
    }

}
