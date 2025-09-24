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
        Schema::table('asset_pages', function (Blueprint $table) {
            $table->boolean('show_pic_chart')->default(true)->after('icon');
            $table->boolean('show_project_chart')->default(true)->after('show_pic_chart');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_pages', function (Blueprint $table) {
            $table->dropColumn(['show_pic_chart', 'show_project_chart']);
        });
    }
};
