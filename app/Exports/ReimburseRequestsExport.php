<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReimburseRequestsExport extends BaseExport implements FromCollection, WithHeadings, WithCustomCsvSettings, WithColumnFormatting
{
    protected $requests;

    public function __construct($requests)
    {
        $this->requests = $requests;
    }

    public function collection()
    {
        return collect($this->requests)->map(function($req) {
            return [
                $req->nomor_pengajuan ?: '-',
                $req->user ? $req->user->name : '-',
                $req->asset ? ($req->asset->merk . ' ' . $req->asset->tipe) : '-',
                $req->jenis_reimburse ?: '-',
                $req->biaya ? (float) $req->biaya : 0.0,
                $req->status ?: '-',
                $req->created_at ? $req->created_at->format('d/m/Y H:i') : '-',
                $req->approved_at ? $req->approved_at->format('d/m/Y H:i') : '-',
                $req->keterangan ?: '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No. Pengajuan', 'User', 'Asset', 'Jenis Reimburse', 'Biaya', 'Status', 'Tanggal Pengajuan', 'Tanggal Approved', 'Keterangan'
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
        // E = Biaya
        return [
            'E' => '"Rp " #,##0',
        ];
    }
}
