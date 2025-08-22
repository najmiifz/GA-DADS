<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehiclesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Asset::where('tipe','Kendaraan')->get();
    }

    public function headings(): array
    {
        return [
            'ID','Jenis Aset','PIC','Merk','Project',
            'Lokasi','Tahun Beli','Harga Beli','Harga Sewa',
            'Status Pajak','Total Servis'
        ];
    }
}
