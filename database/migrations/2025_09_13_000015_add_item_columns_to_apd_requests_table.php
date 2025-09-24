<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('apd_requests', function (Blueprint $table) {
            // Add item count columns and lokasi project
            if (! Schema::hasColumn('apd_requests', 'helm')) {
                $table->integer('helm')->default(0)->after('team_mandor');
            }
            if (! Schema::hasColumn('apd_requests', 'rompi')) {
                $table->integer('rompi')->default(0)->after('helm');
            }
            if (! Schema::hasColumn('apd_requests', 'apboots')) {
                $table->integer('apboots')->default(0)->after('rompi');
            }
            if (! Schema::hasColumn('apd_requests', 'body_harness')) {
                $table->integer('body_harness')->default(0)->after('apboots');
            }
            if (! Schema::hasColumn('apd_requests', 'sarung_tangan')) {
                $table->integer('sarung_tangan')->default(0)->after('body_harness');
            }
            if (! Schema::hasColumn('apd_requests', 'lokasi_project')) {
                $table->string('lokasi_project')->nullable()->after('nama_cluster');
            }
        });
    }

    public function down()
    {
        Schema::table('apd_requests', function (Blueprint $table) {
            $table->dropColumn(['helm','rompi','apboots','body_harness','sarung_tangan','lokasi_project']);
        });
    }
};
