<?php

namespace App\Models;

use App\Models\ApdRequest;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Bootstrap the model and add observer for lokasi changes
     */
    protected static function booted()
    {
        static::updated(function ($user) {
            // If lokasi attribute changed, sync to all assets assigned to this user
            if ($user->wasChanged('lokasi')) {
                \App\Models\Asset::where('user_id', $user->id)
                    ->update(['lokasi' => $user->lokasi]);
            }
            // If project changed, sync to all assets assigned to this user
            if ($user->wasChanged('project')) {
                \App\Models\Asset::where('user_id', $user->id)
                    ->update(['project' => $user->project]);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nik',
        'password',
        'role',
        'lokasi',
        'project',
        'jabatan',
        'email_verified_at',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Relation to ServiceRequest
     */
    public function serviceRequests()
    {
        return $this->hasMany(\App\Models\ServiceRequest::class);
    }

    // Get full URL to avatar image
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . ltrim($this->avatar, '/'));
        }
        return null;
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function apdRequests()
    {
        return $this->hasMany(ApdRequest::class);
    }
    public function reimburseRequests()
    {
        return $this->hasMany(ReimburseRequest::class);
    }
    /**
     * Relation to SpjRequest
     */
    public function spjRequests()
    {
        return $this->hasMany(\App\Models\SpjRequest::class);
    }
}
