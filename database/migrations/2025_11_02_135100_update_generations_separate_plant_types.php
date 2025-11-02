<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('generations', function (Blueprint $table) {
            // Add two separate plant type columns
            $table->json('nectar_plants')->nullable()->after('host_plants')->comment('JSON array of plant IDs for nectar plants (Nektarpflanzen)');
            $table->json('larval_host_plants')->nullable()->after('nectar_plants')->comment('JSON array of plant IDs for host plants (Futterpflanzen for larvae)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generations', function (Blueprint $table) {
            $table->dropColumn(['nectar_plants', 'larval_host_plants']);
        });
    }
};
