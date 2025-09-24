<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pengajuan',
        'lokasi_project',
        'asset_id',
        'user_id',
        'km_saat_ini',
        'keluhan',
        'estimasi_harga',
        'foto_estimasi',
        'foto_km',
        'status',
        'catatan_admin',
        'approved_by',
        'approved_at',
        'biaya_servis',
        'foto_invoice',
        'tanggal_servis',
        'keterangan_servis',
        'foto_struk_service',
        'tanggal_selesai_service',
        'catatan_user',
    'foto_bukti_service',
    'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'foto_km' => 'array',
        'foto_estimasi' => 'array',
        'foto_invoice' => 'array',
    'foto_struk_service' => 'array',
    'foto_bukti_service' => 'array',
        'approved_at' => 'datetime',
        'verified_at' => 'datetime',
        'tanggal_servis' => 'datetime',
        'tanggal_selesai_service' => 'datetime',
        'biaya_servis' => 'decimal:2',
        'estimasi_harga' => 'decimal:2'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Menunggu</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Disetujui</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Ditolak</span>',
            'service_pending' => '<span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Service Berlangsung</span>',
            'service_completed' => '<span class="px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full">Menunggu Verifikasi</span>',
            'verified' => '<span class="px-2 py-1 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded-full">Terverifikasi</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">Selesai</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">Unknown</span>'
        };
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isServicePending()
    {
        return $this->status === 'service_pending';
    }

    public function isServiceCompleted()
    {
        return $this->status === 'service_completed';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canBeApproved()
    {
        return $this->isPending();
    }

    public function canBeRejected()
    {
        return $this->isPending();
    }

    public function canBeCompleted()
    {
        return $this->isApproved() || $this->isInProgress();
    }

    public function canBeServicePending()
    {
        return $this->isApproved();
    }

    public function canCompleteService()
    {
        return $this->isServicePending();
    }

    public function canBeVerified()
    {
        return $this->isServiceCompleted();
    }

    // Removed generateNomorPengajuan(): numbering is now based on auto-increment ID post-creation in controller.

    public function getFotoKmUrlsAttribute()
    {
        if (!$this->foto_km) return [];

        return collect($this->foto_km)->map(function ($filename) {
            return Storage::url('service-requests/km/' . $filename);
        })->toArray();
    }

    public function getFotoInvoiceUrlsAttribute()
    {
        if (!$this->foto_invoice) return [];

        return collect($this->foto_invoice)->map(function ($filename) {
            return Storage::url('service-requests/invoices/' . $filename);
        })->toArray();
    }

    public function getFotoEstimasiUrlsAttribute()
    {
        if (!$this->foto_estimasi) return [];

        return collect($this->foto_estimasi)->map(function ($filename) {
            return Storage::url('service-requests/estimates/' . $filename);
        })->toArray();
    }

    public function getFotoStrukServiceUrlsAttribute()
    {
        if (!$this->foto_struk_service) return [];

        return collect($this->foto_struk_service)->map(function ($filename) {
            return Storage::url('service-requests/service-receipts/' . $filename);
        })->toArray();
    }
    /**
     * Get URLs for proof of service evidence photos.
     *
     * @return array
     */
    public function getFotoBuktiServiceUrlsAttribute()
    {
        if (!$this->foto_bukti_service) return [];

        return collect($this->foto_bukti_service)->map(function ($filename) {
            return Storage::url('service-requests/service-evidence/' . $filename);
        })->toArray();
    }
}
