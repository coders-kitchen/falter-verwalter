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
        Schema::create('endangered_regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique(); // e.g., NRW, WB, BGL, etc.
            $table->string('name')->unique(); // Full name
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('user_id');
        });

        // Pivot table for species endangered regions
        Schema::create('species_endangered_region', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->foreignId('endangered_region_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['species_id', 'endangered_region_id']);
            $table->index('species_id');
            $table->index('endangered_region_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('species_endangered_region');
        Schema::dropIfExists('endangered_regions');
    }
};
