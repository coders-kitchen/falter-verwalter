<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('species_distribution_areas', function (Blueprint $table) {
            if (!Schema::hasColumn('species_distribution_areas', 'threat_category_id')) {
                $table->foreignId('threat_category_id')->nullable()->constrained('threat_categories')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('species_distribution_areas', 'user_id')) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('species_distribution_areas', function (Blueprint $table) {
            $table->dropColumn('threat_category_id');
            $table->dropColumn('user_id');
        });
    }
};
