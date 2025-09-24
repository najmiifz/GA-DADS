<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('apd_requests', function (Blueprint $table) {
            $table->string('lokasi_project')->nullable()->after('nama_cluster');
        });
    }

    public function down()
    {
        Schema::table('apd_requests', function (Blueprint $table) {
            $table->dropColumn('lokasi_project');
        });
    }
};
