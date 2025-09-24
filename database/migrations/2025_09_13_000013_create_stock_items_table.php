<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('stock')->default(50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_items');
    }
};
