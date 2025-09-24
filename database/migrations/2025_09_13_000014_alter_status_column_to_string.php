<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Change status column from enum to string to remove CHECK constraint
        // Requires doctrine/dbal package
        Schema::table('apd_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    public function down()
    {
        // Revert status column back to enum including 'delivery'
        Schema::table('apd_requests', function (Blueprint $table) {
            $table->enum('status', ['pending','delivery','approved','rejected'])->default('pending')->change();
        });
    }
};
