<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reimburse_requests', function (Blueprint $table) {
            $table->text('foto_bukti_service')->nullable()->after('bukti_struk');
        });
    }

    public function down()
    {
        Schema::table('reimburse_requests', function (Blueprint $table) {
            $table->dropColumn('foto_bukti_service');
        });
    }
};
