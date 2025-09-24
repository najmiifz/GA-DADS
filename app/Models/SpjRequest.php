<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpjRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pengajuan',
        'user_id',
        'bast_mutasi',
        'bast_mutasi_file',
        'bast_inventaris',
        'bast_inventaris_file',
        'nama_pegawai',
        'keperluan',
        'lokasi_project',
        'penugasan_by',
        'bukti_penugasan_file',
        'perjalanan_from_to',
        'spj_date',
        'transportasi',
        'biaya_estimasi',
        'nota_files',
        'status',
    ];

    protected $casts = [
        'nota_files' => 'array',
        'spj_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: perjalanan_from derived from perjalanan_from_to (stored as "from - to").
     */
    public function getPerjalananFromAttribute()
    {
        if (empty($this->perjalanan_from_to)) return null;
        $parts = explode(' - ', $this->perjalanan_from_to, 2);
        return $parts[0] ?? null;
    }

    /**
     * Accessor: perjalanan_to derived from perjalanan_from_to (stored as "from - to").
     */
    public function getPerjalananToAttribute()
    {
        if (empty($this->perjalanan_from_to)) return null;
        $parts = explode(' - ', $this->perjalanan_from_to, 2);
        return $parts[1] ?? null;
    }
}
