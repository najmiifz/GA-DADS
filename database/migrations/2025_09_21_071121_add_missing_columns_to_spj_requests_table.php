<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spj_requests', function (Blueprint $table) {
            $table->string('nomor_pengajuan')->nullable()->after('id');
            $table->string('lokasi_project')->nullable()->after('keperluan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spj_requests', function (Blueprint $table) {
            $table->dropColumn(['nomor_pengajuan', 'lokasi_project']);
        });
    }
};
