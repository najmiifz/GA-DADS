<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PajakHistory extends Model
{
    use HasFactory;

    protected $table = 'pajak_histories';

    protected $fillable = [
        'asset_id',
        'tanggal_pajak',
        'jumlah_pajak',
        'status_pajak',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
