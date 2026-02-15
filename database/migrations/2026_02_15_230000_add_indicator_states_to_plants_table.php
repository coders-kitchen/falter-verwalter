<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->string('light_number_state', 10)->default('numeric');
            $table->string('salt_number_state', 10)->default('numeric');
            $table->string('temperature_number_state', 10)->default('numeric');
            $table->string('continentality_number_state', 10)->default('numeric');
            $table->string('reaction_number_state', 10)->default('numeric');
            $table->string('moisture_number_state', 10)->default('numeric');
            $table->string('moisture_variation_state', 10)->default('numeric');
            $table->string('nitrogen_number_state', 10)->default('numeric');
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropColumn([
                'light_number_state',
                'salt_number_state',
                'temperature_number_state',
                'continentality_number_state',
                'reaction_number_state',
                'moisture_number_state',
                'moisture_variation_state',
                'nitrogen_number_state',
            ]);
        });
    }
};
