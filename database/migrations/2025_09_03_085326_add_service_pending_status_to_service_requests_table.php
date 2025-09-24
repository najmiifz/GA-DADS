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
        Schema::table('service_requests', function (Blueprint $table) {
            // Change the enum to include new statuses
            $table->enum('status', ['pending', 'approved', 'rejected', 'service_pending', 'service_completed', 'verified', 'completed'])->default('pending')->change();

            // Add fields for service completion by user
            $table->json('foto_struk_service')->nullable()->after('foto_invoice');
            $table->timestamp('tanggal_selesai_service')->nullable()->after('tanggal_servis');
            $table->text('catatan_user')->nullable()->after('keterangan_servis');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null')->after('approved_by');
            $table->timestamp('verified_at')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed'])->default('pending')->change();
            $table->dropColumn(['foto_struk_service', 'tanggal_selesai_service', 'catatan_user', 'verified_by', 'verified_at']);
        });
    }
};
