<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AssetPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'asset_type',
        'icon',
        'chart_config',
    ];

    protected $casts = [
        'chart_config' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            $page->slug = Str::slug($page->name);
        });
    }
}
