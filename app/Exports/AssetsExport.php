<?php
namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class AssetsExport extends BaseExport implements FromCollection, WithCustomCsvSettings, WithColumnFormatting
{
    protected $filters;
    protected $type;

    public function __construct(array $filters = [], $type = 'basic')
    {
        $this->filters = $filters;
        $this->type = $type;
    }

    public function collection()
    {
        $query = Asset::with('user');

        if ($this->type === 'full') {
            $query->with('services', 'pajakHistory');
        }

        if (!empty($this->filters)) {
            if (isset($this->filters['pic'])) $query->where('pic', 'like', '%'.$this->filters['pic'].'%');
            if (isset($this->filters['tipe'])) $query->where('tipe', $this->filters['tipe']);
            if (isset($this->filters['project'])) $query->where('project', $this->filters['project']);
            if (isset($this->filters['lokasi'])) $query->where('lokasi', $this->filters['lokasi']);
            if (isset($this->filters['jenis_aset'])) $query->where('jenis_aset', $this->filters['jenis_aset']);
        }

        $result = $query->get();

        return $result->map(function ($asset) {
            $picName = $asset->user && $asset->user->name ? $asset->user->name : ($asset->pic ?? '');

            $data = [
                $asset->tipe,
                $asset->jenis_aset,
                $picName,
                optional($asset->user)->nik ?? '',
                $asset->merk,
                $asset->serial_number ?? '',
                $asset->project,
                $asset->lokasi,
                $asset->tanggal_beli ? Carbon::parse($asset->tanggal_beli)->format('d M Y') : '',
                $asset->harga_beli ? (float) $asset->harga_beli : 0.0,
                $asset->harga_sewa ? (float) $asset->harga_sewa : 0.0,
            ];

            if ($this->type === 'full') {
                $serviceHistory = $asset->services->map(function($service) {
                    $cost = number_format($service->cost ?? 0, 0, ',', '.');
                    $date = Carbon::parse($service->service_date)->format('d/m/Y');
                    return "{$date}: {$service->description} (Rp {$cost})";
                })->implode('; ');

                $pajakHistory = $asset->pajakHistory->map(function($pajak) {
                    $amount = number_format($pajak->jumlah_pajak ?? 0, 0, ',', '.');
                    $date = Carbon::parse($pajak->tanggal_pajak)->format('d/m/Y');
                    return "{$date}: {$pajak->status_pajak} (Rp {$amount})";
                })->implode('; ');

                $data[] = $serviceHistory;
                $data[] = $pajakHistory;
            }

            return $data;
        });
    }

    public function headings(): array
    {
        $headings = [
            'Tipe','Jenis Aset','PIC','NIK','Merk','Nomor Aset','Project',
            'Lokasi','Tanggal Beli','Harga Beli','Harga Sewa',
        ];

        if ($this->type === 'full') {
            $headings[] = 'Riwayat Servis';
            $headings[] = 'Riwayat Pajak';
        }

        return $headings;
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
        // J = Harga Beli, K = Harga Sewa
        return [
            'J' => '"Rp " #,##0',
            'K' => '"Rp " #,##0',
        ];
    }
}
