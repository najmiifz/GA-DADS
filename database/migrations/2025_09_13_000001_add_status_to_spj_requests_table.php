<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('spj_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('nota_files');
        });
    }

    public function down()
    {
        Schema::table('spj_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
