<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_areas', function (Blueprint $table) {
            $table->string('geojson_path')->nullable()->after('description');
            $table->index('geojson_path');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_areas', function (Blueprint $table) {
            $table->dropIndex(['geojson_path']);
            $table->dropColumn('geojson_path');
        });
    }
};
