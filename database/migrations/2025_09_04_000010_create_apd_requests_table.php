<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apd_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('team_mandor');
            $table->integer('jumlah_apd');
            $table->string('nama_cluster');
            $table->enum('status', ['pending', 'delivery', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apd_requests');
    }
};
