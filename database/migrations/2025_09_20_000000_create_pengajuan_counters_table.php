<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengajuan_counters', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('year_month');
            $table->unsignedBigInteger('last_seq')->default(0);
            $table->timestamps();
            $table->unique(['type', 'year_month'], 'pengajuan_counters_type_ym_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_counters');
    }
};
