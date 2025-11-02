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
        Schema::create('species_region', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->enum('conservation_status', ['nicht_gefährdet', 'gefährdet'])->default('nicht_gefährdet');
            $table->timestamps();

            // Ensure no duplicate species-region combinations
            $table->unique(['species_id', 'region_id']);

            // Indexes for querying
            $table->index(['species_id', 'conservation_status']);
            $table->index('region_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('species_region');
    }
};
