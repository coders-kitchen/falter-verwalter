<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threat_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('label', 40)->unique();
            $table->string('description');
            $table->integer('rank')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Ensure no duplicate species-region combinations
            $table->unique(['code', 'label']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threat_categories');
    }
};
