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
            // Drop old boolean columns if they exist
            if (Schema::hasColumn('asset_pages', 'show_pic_chart')) {
                $table->dropColumn('show_pic_chart');
            }
            if (Schema::hasColumn('asset_pages', 'show_project_chart')) {
                $table->dropColumn('show_project_chart');
            }

            // Add a single JSON column for flexible chart configuration
            $table->json('chart_config')->nullable()->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_pages', function (Blueprint $table) {
            if (Schema::hasColumn('asset_pages', 'chart_config')) {
                $table->dropColumn('chart_config');
            }

            // Add old columns back for rollback
            $table->boolean('show_pic_chart')->default(true);
            $table->boolean('show_project_chart')->default(true);
        });
    }
};
