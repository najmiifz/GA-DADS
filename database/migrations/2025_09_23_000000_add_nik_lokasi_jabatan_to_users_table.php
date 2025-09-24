<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'lokasi')) {
                $table->string('lokasi')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('lokasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'jabatan')) {
                $table->dropColumn('jabatan');
            }
            if (Schema::hasColumn('users', 'lokasi')) {
                $table->dropColumn('lokasi');
            }
            if (Schema::hasColumn('users', 'nik')) {
                $table->dropColumn('nik');
            }
        });
    }
};
