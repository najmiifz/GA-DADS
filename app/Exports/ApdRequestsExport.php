<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ApdRequestsExport extends BaseExport implements FromCollection, WithHeadings, WithCustomCsvSettings, WithColumnFormatting
{
    protected $requests;

    public function __construct($requests)
    {
        $this->requests = $requests;
    }

    public function collection()
    {
        return collect($this->requests)->map(function($r) {
            return [
                $r->nomor_pengajuan ?: '-',
                $r->nama_cluster ?: '-',
                $r->user ? $r->user->name : '-',
                $r->helm ?: 0,
                $r->rompi ?: 0,
                $r->apboots ?: 0,
                $r->body_harness ?: 0,
                $r->sarung_tangan ?: 0,
                $r->jumlah_apd ?: 0,
                ucfirst($r->status ?? '-'),
                $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-',
                $r->approved_at ? $r->approved_at->format('d/m/Y H:i') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No. Pengajuan','Nama Cluster','PIC','Helm','Rompi','AP Boots','Body Harness','Sarung Tangan','Total APD','Status','Tanggal Pengajuan','Tanggal Approved'
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
        // No currency columns here, but ensure autosize from BaseExport
        return [];
    }
}
