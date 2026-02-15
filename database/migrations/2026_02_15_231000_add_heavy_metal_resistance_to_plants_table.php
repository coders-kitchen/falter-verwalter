<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->string('heavy_metal_resistance')
                ->default('nicht schwermetallresistent');
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropColumn('heavy_metal_resistance');
        });
    }
};
