<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subfamilies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->cascadeOnDelete();
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['family_id', 'name']);
            $table->index('name');
        });

        Schema::create('tribes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subfamily_id')->constrained('subfamilies')->cascadeOnDelete();
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['subfamily_id', 'name']);
            $table->index('name');
        });

        Schema::create('genera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subfamily_id')->constrained('subfamilies')->cascadeOnDelete();
            $table->foreignId('tribe_id')->nullable()->constrained('tribes')->nullOnDelete();
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['subfamily_id', 'tribe_id', 'name']);
            $table->index('name');
        });

        Schema::table('species', function (Blueprint $table) {
            $table->foreignId('genus_id')->nullable()->after('family_id')->constrained('genera')->nullOnDelete();
            $table->index('genus_id');
        });

        Schema::table('plants', function (Blueprint $table) {
            $table->foreignId('genus_id')->nullable()->after('family_id')->constrained('genera')->nullOnDelete();
            $table->index('genus_id');
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('genus_id');
        });

        Schema::table('species', function (Blueprint $table) {
            $table->dropConstrainedForeignId('genus_id');
        });

        Schema::dropIfExists('genera');
        Schema::dropIfExists('tribes');
        Schema::dropIfExists('subfamilies');
    }
};
