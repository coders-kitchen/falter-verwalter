<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('distribution_areas', 'geometry_geojson')) {
            Schema::table('distribution_areas', function (Blueprint $table) {
                $table->dropColumn('geometry_geojson');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('distribution_areas', 'geometry_geojson')) {
            Schema::table('distribution_areas', function (Blueprint $table) {
                $table->json('geometry_geojson')->nullable()->after('geojson_path');
            });
        }
    }
};
