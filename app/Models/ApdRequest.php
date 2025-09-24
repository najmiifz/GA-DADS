<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApdRequest extends Model
{
    use HasFactory;

    protected $fillable = [
    'nomor_pengajuan', 'user_id', 'team_mandor', 'jumlah_apd', 'nama_cluster',
        'helm', 'rompi', 'apboots', 'body_harness', 'sarung_tangan',
    'lokasi_project', 'status', 'approved_at', 'restocked'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
