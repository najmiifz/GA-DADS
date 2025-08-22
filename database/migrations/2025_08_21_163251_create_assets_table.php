<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('tipe'); // Kendaraan, Splicer, Elektronik, dll
            $table->string('jenis_aset');
            $table->string('pic')->nullable();
            $table->string('merk')->nullable();
            $table->string('project')->nullable();
            $table->string('lokasi')->nullable();
            $table->year('tahun_beli')->nullable();
            $table->decimal('harga_beli',15,2)->default(0);
            $table->decimal('harga_sewa',15,2)->default(0);
            $table->enum('status_pajak',['Lunas','Belum Lunas','Tidak Lengkap'])->nullable();
            $table->decimal('total_servis',15,2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('assets');
    }
};
