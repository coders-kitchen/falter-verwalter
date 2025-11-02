<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habitats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('habitats')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('level')->default(1);
            $table->timestamps();

            $table->index(['parent_id', 'level']);
            $table->index('user_id');
            $table->unique(['user_id', 'name', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habitats');
    }
};
