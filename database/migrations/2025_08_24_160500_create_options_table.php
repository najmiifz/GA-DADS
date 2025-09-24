<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // e.g., tipe, jenis_aset, pic, project, lokasi
            $table->string('name');
            $table->timestamps();
            $table->unique(['category','name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('options');
    }
};
