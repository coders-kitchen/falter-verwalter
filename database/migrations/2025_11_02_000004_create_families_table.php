<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('subfamily', 100)->nullable();
            $table->string('genus', 100)->nullable();
            $table->string('tribe', 100)->nullable();
            $table->enum('type', ['butterfly', 'plant'])->default('butterfly');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('user_id');
            $table->index('type');
            $table->unique(['name', 'subfamily', 'genus', 'tribe', 'type'], 'families_hierarchical_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
