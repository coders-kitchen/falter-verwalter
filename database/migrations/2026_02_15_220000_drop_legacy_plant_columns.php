<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            if (Schema::hasColumn('plants', 'bloom_months')) {
                $table->dropColumn('bloom_months');
            }

            if (Schema::hasColumn('plants', 'plant_height_cm')) {
                $table->dropColumn('plant_height_cm');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            if (!Schema::hasColumn('plants', 'bloom_months')) {
                $table->json('bloom_months')->nullable();
            }

            if (!Schema::hasColumn('plants', 'plant_height_cm')) {
                $table->integer('plant_height_cm')->nullable();
            }
        });
    }
};
