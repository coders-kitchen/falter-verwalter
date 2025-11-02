<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // species_distribution pivot
        Schema::create('species_distribution', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_area_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['species_id', 'distribution_area_id']);
        });

        // species_habitat pivot
        Schema::create('species_habitat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->foreignId('habitat_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['species_id', 'habitat_id']);
        });

        // species_plant pivot (for host/food plants)
        Schema::create('species_plant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plant_id')->constrained()->cascadeOnDelete();
            $table->enum('plant_type', ['host', 'nectar', 'other'])->default('host');
            $table->timestamps();

            $table->unique(['species_id', 'plant_id']);
        });

        // plant_habitat pivot
        Schema::create('plant_habitat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('habitat_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['plant_id', 'habitat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plant_habitat');
        Schema::dropIfExists('species_plant');
        Schema::dropIfExists('species_habitat');
        Schema::dropIfExists('species_distribution');
    }
};
