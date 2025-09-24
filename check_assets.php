#!/usr/bin/env php
<?php

// Change to the Laravel project directory
chdir(__DIR__);

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Asset;
use App\Models\User;

echo "Checking all assets in system...\n";

$allAssets = Asset::all();
echo "Total assets: " . $allAssets->count() . "\n\n";

if ($allAssets->count() > 0) {
    echo "All assets:\n";
    foreach ($allAssets as $asset) {
        $assignedTo = $asset->user_id ? User::find($asset->user_id)->name : 'Unassigned';
        echo "- Asset {$asset->id}: {$asset->merk} {$asset->tipe} (Assigned to: {$assignedTo})\n";
    }
}

echo "\nDone!\n";
