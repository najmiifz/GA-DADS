<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanCounter extends Model
{
    protected $table = 'pengajuan_counters';
    protected $fillable = ['type', 'year_month', 'last_seq'];
}
