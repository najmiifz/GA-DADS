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
        Schema::table('assets', function (Blueprint $table) {
            // Add asset photo
            $table->string('foto_aset')->nullable()->after('foto_kendaraan');
            // Add keterangan field
            $table->text('keterangan')->nullable()->after('foto_aset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['foto_aset', 'keterangan']);
        });
    }
};
