<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$rows = App\Models\ApdRequest::orderBy('created_at','desc')->get();
foreach($rows as $r) {
    echo sprintf("ID:%s No:%s Status:%s Approved:%s\n", $r->id, $r->nomor_pengajuan, $r->status, $r->approved_at ? $r->approved_at->toDateTimeString() : '-');
}
