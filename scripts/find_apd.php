<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ApdRequest;

// If argument provided, search by nomor_pengajuan
$needle = $argv[1] ?? null;
if ($needle) {
    $r = ApdRequest::where('nomor_pengajuan', $needle)->with('user')->first();
    if (!$r) {
        echo "No APD found with nomor_pengajuan={$needle}\n";
        exit;
    }
    printf("ID:%s nomor:%s status:%s user:%s approved_at:%s\n", $r->id, $r->nomor_pengajuan, $r->status, $r->user? $r->user->name : '-', $r->approved_at ? $r->approved_at->toDateTimeString() : '-');
    exit;
}

$rows = ApdRequest::with('user')->orderBy('created_at','desc')->take(20)->get();
foreach ($rows as $r) {
    printf("ID:%s nomor:%s status:%s user:%s created:%s approved:%s\n", $r->id, $r->nomor_pengajuan, $r->status, $r->user? $r->user->name : '-', $r->created_at->toDateTimeString(), $r->approved_at ? $r->approved_at->toDateTimeString() : '-');
}
