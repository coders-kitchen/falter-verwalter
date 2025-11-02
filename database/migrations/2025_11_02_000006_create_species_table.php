<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('scientific_name')->nullable();
            $table->enum('size_category', ['XS', 'S', 'M', 'L', 'XL']);
            $table->text('color_description')->nullable();
            $table->text('special_features')->nullable();
            $table->text('gender_differences')->nullable();
            $table->integer('generations_per_year')->nullable();
            $table->enum('hibernation_stage', ['egg', 'larva', 'pupa', 'adult'])->nullable();
            $table->integer('pupal_duration_days')->nullable();
            $table->string('red_list_status_de')->nullable();
            $table->string('red_list_status_eu')->nullable();
            $table->string('abundance_trend')->nullable();
            $table->string('protection_status')->nullable();
            $table->timestamps();

            $table->index(['family_id', 'size_category']);
            $table->index('user_id');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('species');
    }
};
