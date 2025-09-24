<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // No-op: avatar column already added in initial users migration
    }

    public function down()
    {
        // No-op: avoid dropping avatar column added by initial migration
    }
};
