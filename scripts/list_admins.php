<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
$users = DB::table('users')->whereIn('role', ['admin','super-admin'])->select('id','name','email','role')->get();
if(count($users) === 0) {
    echo "NO_ADMINS\n";
    exit(0);
}
foreach($users as $u) {
    echo $u->id . '|' . $u->name . '|' . $u->email . '|' . $u->role . "\n";
}
