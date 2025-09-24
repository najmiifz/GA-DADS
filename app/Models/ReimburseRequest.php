<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReimburseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pengajuan', 'user_id', 'asset_id', 'biaya', 'keterangan', 'tanggal_service', 'bukti_struk', 'foto_bukti_service', 'status', 'approved_at'
    ];

    protected $casts = [
        'tanggal_service' => 'date',
        'approved_at' => 'datetime',
        'bukti_struk' => 'array',
        'foto_bukti_service' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
