<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('species_distribution_area');
    }

    public function down(): void
    {
        Schema::create('species_endagered_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->foreignId('distribution_area_id')->constrained('distribution_areas')->cascadeOnDelete();
            $table->foreignId('threat_category_id')->constrained('threat_categories')->cascadeOnDelete();
            $table->timestamps();

            // Ensure no duplicate species-region combinations
            $table->unique(['species_id', 'distribution_area_id']);

            // Indexes for querying
            $table->index(['species_id', 'threat_category_id']);
            $table->index('distribution_area_id');
        });
    }
};
