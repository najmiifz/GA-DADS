<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('spj_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bast_mutasi')->nullable();
            $table->string('bast_mutasi_file')->nullable();
            $table->text('bast_inventaris')->nullable();
            $table->string('bast_inventaris_file')->nullable();
            $table->string('nama_pegawai');
            $table->text('keperluan');
            $table->string('penugasan_by');
            $table->string('bukti_penugasan_file')->nullable();
            $table->string('perjalanan_from_to');
            $table->date('spj_date');
            $table->string('transportasi');
            $table->text('biaya_estimasi');
            $table->json('nota_files')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('spj_requests');
    }
};
