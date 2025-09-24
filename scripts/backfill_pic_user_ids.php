<?php

require __DIR__ . "/../vendor/autoload.php";

// Minimal bootstrap for Laravel's Eloquent outside of artisan isn't ideal; instead we'll run this via php artisan tinker-like environment.
// But create a simple script that can be executed with `php artisan tinker --execute="require 'scripts/backfill_pic_user_ids.php'"` or included via artisan command.

// The simplest approach: use Laravel's framework by bootstrapping the app.
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

$rows = Asset::whereNotNull('pic')->get();
$updated = 0;
foreach ($rows as $asset) {
    // if pic is numeric or matches an existing user id
    if (is_numeric($asset->pic)) {
        $user = User::find(intval($asset->pic));
        if ($user) {
            $asset->user_id = $user->id;
            $asset->pic = $user->name;
            $asset->save();
            $updated++;
            echo "Updated asset {$asset->id} -> user_id={$user->id}, pic={$user->name}\n";
        }
    } else {
        // Also attempt to find user by id stored as string in user_id column but pic is name
        if ($asset->user_id && is_numeric($asset->user_id)) {
            $user = User::find($asset->user_id);
            if ($user && $asset->pic !== $user->name) {
                $asset->pic = $user->name;
                $asset->save();
                $updated++;
                echo "Synced asset {$asset->id} pic to user name {$user->name}\n";
            }
        }
    }
}

echo "Done. Updated {$updated} assets.\n";
