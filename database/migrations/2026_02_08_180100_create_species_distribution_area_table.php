<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('species_distribution_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->foreignId('distribution_area_id')->constrained('distribution_areas')->cascadeOnDelete();
            $table->enum('status', ['heimisch', 'ausgestorben', 'neobiotisch'])->default('heimisch');
            $table->timestamps();

            // Ensure no duplicate species-region combinations
            $table->unique(['species_id', 'distribution_area_id'], 'species_dist_area');

            // Indexes for querying
            $table->index(['species_id', 'status']);
            $table->index('distribution_area_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('species_distribution_areas');
    }
};
