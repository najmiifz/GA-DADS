<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('apd_requests', function (Blueprint $table) {
            $table->integer('helm')->default(0);
            $table->integer('rompi')->default(0);
            $table->integer('apboots')->default(0);
            $table->integer('body_harness')->default(0);
            $table->integer('sarung_tangan')->default(0);
        });
    }

    public function down()
    {
        Schema::table('apd_requests', function (Blueprint $table) {
            $table->dropColumn(['helm', 'rompi', 'apboots', 'body_harness', 'sarung_tangan']);
        });
    }
};
