#!/usr/bin/env php
<?php

// Change to the Laravel project directory
chdir(__DIR__);

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Checking assets table schema...\n";

$columns = Schema::getColumnListing('assets');
echo "Columns in assets table:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}

echo "\nDone!\n";
