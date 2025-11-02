<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('life_form_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('scientific_name')->nullable();
            $table->string('family_genus')->nullable();

            // Ecological scales (1-9)
            $table->integer('light_number')->nullable();
            $table->integer('temperature_number')->nullable();
            $table->integer('continentality_number')->nullable();
            $table->integer('reaction_number')->nullable();
            $table->integer('moisture_number')->nullable();
            $table->integer('moisture_variation')->nullable();
            $table->integer('nitrogen_number')->nullable();

            // Botanical attributes
            $table->json('bloom_months')->nullable();
            $table->string('bloom_color')->nullable();
            $table->integer('plant_height_cm')->nullable();
            $table->enum('lifespan', ['annual', 'biennial', 'perennial'])->nullable();
            $table->text('location')->nullable();
            $table->boolean('is_native')->default(false);
            $table->boolean('is_invasive')->default(false);
            $table->string('threat_status')->nullable();
            $table->text('persistence_organs')->nullable();

            $table->timestamps();

            $table->index(['life_form_id', 'name']);
            $table->index('user_id');
            $table->index('is_native');
            $table->index('is_invasive');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
