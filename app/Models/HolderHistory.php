<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolderHistory extends Model
{
    use HasFactory;

    protected $fillable = ['asset_id','holder_name','start_date','end_date','note'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
