<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceHistory extends Model
{
    use HasFactory;

    protected $fillable = ['asset_id','service_date','description','cost','vendor','file_path'];

    protected $dates = ['created_at', 'updated_at'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Check if this service history can be edited or deleted
     * Returns true if less than 24 hours have passed since creation
     */
    public function canBeModified()
    {
        return $this->created_at->diffInHours(now()) < 24;
    }

    /**
     * Get human readable time remaining for modification
     */
    public function getTimeRemainingForModification()
    {
        if ($this->canBeModified()) {
            $totalMinutesLeft = (24 * 60) - $this->created_at->diffInMinutes(now());
            $hoursLeft = intval($totalMinutesLeft / 60);
            $minutesLeft = $totalMinutesLeft % 60;

            if ($hoursLeft > 0) {
                return $hoursLeft . ' jam ' . $minutesLeft . ' menit';
            } else {
                return $minutesLeft . ' menit';
            }
        }

        return null;
    }

    /**
     * Get the exact time when modification will no longer be allowed
     */
    public function getModificationDeadline()
    {
        return $this->created_at->addHours(24);
    }
}
