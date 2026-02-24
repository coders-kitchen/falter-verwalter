<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('species_genus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genus_id')->constrained('genera')->cascadeOnDelete();
            $table->boolean('is_nectar')->default(false);
            $table->boolean('is_larval_host')->default(false);
            $table->enum('adult_preference', ['primaer', 'sekundaer'])->nullable();
            $table->enum('larval_preference', ['primaer', 'sekundaer'])->nullable();
            $table->timestamps();

            $table->unique(['species_id', 'genus_id']);
            $table->index('species_id');
            $table->index('genus_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('species_genus');
    }
};
