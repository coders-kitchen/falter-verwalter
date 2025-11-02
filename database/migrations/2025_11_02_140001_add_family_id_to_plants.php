<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            // Add family relationship - optional for now since existing plants won't have it
            $table->foreignId('family_id')->nullable()->after('life_form_id')->constrained()->cascadeOnDelete();
            $table->index('family_id');
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropForeignKey(['family_id']);
            $table->dropIndex(['family_id']);
            $table->dropColumn('family_id');
        });
    }
};
