<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('species_tag', function (Blueprint $table) {
            $table->foreignId('species_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();

            $table->unique(['species_id', 'tag_id']);
            $table->index(['tag_id', 'species_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('species_tag');
        Schema::dropIfExists('tags');
    }
};
