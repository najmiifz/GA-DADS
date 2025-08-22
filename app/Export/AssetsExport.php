<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Asset::all();
    }

    public function headings(): array
    {
        return [
            'ID','Tipe','Jenis Aset','PIC','Merk','Project',
            'Lokasi','Tahun Beli','Harga Beli','Harga Sewa',
            'Status Pajak','Total Servis'
        ];
    }
}
