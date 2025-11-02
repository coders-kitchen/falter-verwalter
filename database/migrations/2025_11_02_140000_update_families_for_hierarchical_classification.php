<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is now handled in the base families table migration
        // Keeping this file for consistency, but operations are performed in 2025_11_02_000004_create_families_table.php
    }

    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropUnique('families_hierarchical_unique');
            $table->dropIndex(['type']);
            $table->dropColumn(['subfamily', 'genus', 'tribe', 'type']);
            $table->unique('name');
        });
    }
};
