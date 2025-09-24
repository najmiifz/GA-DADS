<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
    // Remove leftover old backup table if exists
    Schema::dropIfExists('apd_requests_old');
    // If original table exists, rename it for recreation. Guard in case it's not present (sqlite test runs).
    if (Schema::hasTable('apd_requests')) {
        Schema::rename('apd_requests', 'apd_requests_old');
    }

    // Some sqlite setups can keep index names across renames; drop the specific index if it exists to avoid
    // "index ... already exists" errors when creating a unique column with the same index name.
    try {
        DB::statement('DROP INDEX IF EXISTS apd_requests_nomor_pengajuan_unique');
    } catch (\Exception $e) {
        // ignore — best-effort cleanup for sqlite
    }

        // Create new table with status as string
        Schema::create('apd_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('team_mandor');
            $table->integer('jumlah_apd');
            $table->string('nama_cluster');
            $table->string('status')->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // Copy data
        DB::table('apd_requests')->insertUsing([
            'id', 'nomor_pengajuan', 'user_id', 'team_mandor', 'jumlah_apd', 'nama_cluster', 'status', 'approved_at', 'created_at', 'updated_at'
        ], DB::table('apd_requests_old')->select([
            'id', 'nomor_pengajuan', 'user_id', 'team_mandor', 'jumlah_apd', 'nama_cluster', 'status', 'approved_at', 'created_at', 'updated_at'
        ]));

        // Drop old table
        if (Schema::hasTable('apd_requests_old')) {
            Schema::drop('apd_requests_old');
        }
    }

    public function down()
    {
        // Rollback: rename and restore old enum structure
        Schema::rename('apd_requests', 'apd_requests_new');
        Schema::create('apd_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('team_mandor');
            $table->integer('jumlah_apd');
            $table->string('nama_cluster');
            $table->enum('status', ['pending','delivery','approved','rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
        DB::table('apd_requests')->insertUsing([
            'id', 'nomor_pengajuan', 'user_id', 'team_mandor', 'jumlah_apd', 'nama_cluster', 'status', 'approved_at', 'created_at', 'updated_at'
        ], DB::table('apd_requests_new')->select([
            'id', 'nomor_pengajuan', 'user_id', 'team_mandor', 'jumlah_apd', 'nama_cluster', 'status', 'approved_at', 'created_at', 'updated_at'
        ]));
        Schema::drop('apd_requests_new');
    }
};
