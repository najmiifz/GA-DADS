<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'lokasi_project')) {
                $table->string('lokasi_project')->nullable()->after('user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'lokasi_project')) {
                $table->dropColumn('lokasi_project');
            }
        });
    }
};
