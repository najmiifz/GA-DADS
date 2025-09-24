<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\HolderHistory;
use Carbon\Carbon;

class HolderHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assets = Asset::all();
        foreach ($assets as $asset) {
            // Skip if history already exists
            if ($asset->holderHistories()->count() > 0) {
                continue;
            }

            // Determine holder name: use asset->pic (string or "user:{id}") or fallback to user relation
            $holderName = null;
            if (!empty($asset->pic)) {
                if (str_starts_with($asset->pic, 'user:')) {
                    $userId = substr($asset->pic, 5);
                    $holderName = optional(\App\Models\User::find($userId))->name;
                } else {
                    $holderName = $asset->pic;
                }
            } elseif ($asset->user) {
                $holderName = $asset->user->name;
            }

            if ($holderName) {
                HolderHistory::create([
                    'asset_id'    => $asset->id,
                    'holder_name' => $holderName,
                    'start_date'  => Carbon::parse($asset->created_at)->toDateString(),
                    'note'        => 'Initial assignment of PIC',
                ]);
            }
        }
    }
}
