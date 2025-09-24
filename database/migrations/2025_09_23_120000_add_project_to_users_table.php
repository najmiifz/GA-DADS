<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('users', 'project')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('project')->nullable()->after('lokasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'project')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('project');
            });
        }
    }
};
