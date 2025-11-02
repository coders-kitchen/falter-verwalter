<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->integer('generation_number')->comment('1st, 2nd, 3rd generation');
            $table->integer('larva_start_month')->comment('1-12, month when larvae are active');
            $table->integer('larva_end_month')->comment('1-12, month when larvae period ends');
            $table->integer('flight_start_month')->comment('1-12, month when adults start flying');
            $table->integer('flight_end_month')->comment('1-12, month when adults stop flying');
            $table->json('host_plants')->nullable()->comment('JSON array of {id, type} for host plants');
            $table->text('description')->nullable()->comment('Generation-specific notes');
            $table->timestamps();

            $table->index(['species_id', 'generation_number']);
            $table->index('user_id');
            $table->unique(['species_id', 'generation_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generations');
    }
};
