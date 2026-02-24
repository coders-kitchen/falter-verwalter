<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('changelog_entries', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique();
            $table->string('title');
            $table->text('summary');
            $table->longText('details')->nullable();
            $table->enum('audience', ['public', 'admin', 'both'])->default('both');
            $table->timestamp('published_at')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->json('commit_refs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('changelog_entries');
    }
};
