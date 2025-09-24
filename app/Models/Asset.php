<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    // Ensure Eloquent treats primary key as integer and auto-increments.
    // This helps route model binding and route generation when IDs are numeric.
    public $incrementing = true;
    protected $keyType = 'int';

    // Ensure Eloquent manages created_at/updated_at as datetimes
    // and use $casts to normalize date fields.
    public $timestamps = true;

    protected $fillable = [
        'jenis_aset',
        'merk',
        'model',
    'serial_number',
    'plate_number',
        'harga_beli',
    'tahun_beli',
    'tanggal_beli',
        'tipe',
        'lokasi',
        'project',
        'pic',
        'user_id',
        'kondisi',
    'keterangan',
    'status',
    'harga_sewa',
    'status_pajak',
    'tanggal_pajak',
    'jumlah_pajak',
    'total_servis',
    'foto_stnk',
    'foto_kendaraan',
    'foto_aset'
    ];

    protected $casts = [
        'tahun_beli' => 'integer',
        'tanggal_beli' => 'date',
        'tanggal_pajak' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 'jumlah_pajak' and 'total_servis' can be large, keep as numeric strings or use integer
    ];

    public function services()
    {
        return $this->hasMany(ServiceHistory::class);
    }

    public function reimburseRequests()
    {
        return $this->hasMany(\App\Models\ReimburseRequest::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(\App\Models\ServiceRequest::class);
    }

    public function holderHistories()
    {
        return $this->hasMany(\App\Models\HolderHistory::class);
    }

    public function pajakHistory()
    {
        return $this->hasMany(PajakHistory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recalculateTotalServis()
    {
        $this->total_servis = $this->services()->sum('cost');
        $this->save();
    }
    /**
     * Safely get harga_beli, defaulting to 0 for null or invalid values.
     */
    public function getHargaBeliAttribute($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }
        return (float) $value;
    }
}
