<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('species_plant', function (Blueprint $table) {
            if (!Schema::hasColumn('species_plant', 'adult_phagy_level')) {
                $table->enum('adult_phagy_level', ['unbekannt', 'monophag', 'oligophag', 'polyphag'])
                    ->nullable()
                    ->after('adult_preference');
            }

            if (!Schema::hasColumn('species_plant', 'larval_phagy_level')) {
                $table->enum('larval_phagy_level', ['unbekannt', 'monophag', 'oligophag', 'polyphag'])
                    ->nullable()
                    ->after('larval_preference');
            }
        });

        Schema::table('species_genus', function (Blueprint $table) {
            if (!Schema::hasColumn('species_genus', 'adult_phagy_level')) {
                $table->enum('adult_phagy_level', ['unbekannt', 'monophag', 'oligophag', 'polyphag'])
                    ->nullable()
                    ->after('adult_preference');
            }

            if (!Schema::hasColumn('species_genus', 'larval_phagy_level')) {
                $table->enum('larval_phagy_level', ['unbekannt', 'monophag', 'oligophag', 'polyphag'])
                    ->nullable()
                    ->after('larval_preference');
            }
        });

        DB::table('species_plant')
            ->where('is_nectar', true)
            ->whereNull('adult_phagy_level')
            ->update(['adult_phagy_level' => 'unbekannt']);

        DB::table('species_plant')
            ->where('is_larval_host', true)
            ->whereNull('larval_phagy_level')
            ->update(['larval_phagy_level' => 'unbekannt']);

        DB::table('species_genus')
            ->where('is_nectar', true)
            ->whereNull('adult_phagy_level')
            ->update(['adult_phagy_level' => 'unbekannt']);

        DB::table('species_genus')
            ->where('is_larval_host', true)
            ->whereNull('larval_phagy_level')
            ->update(['larval_phagy_level' => 'unbekannt']);
    }

    public function down(): void
    {
        Schema::table('species_genus', function (Blueprint $table) {
            if (Schema::hasColumn('species_genus', 'larval_phagy_level')) {
                $table->dropColumn('larval_phagy_level');
            }

            if (Schema::hasColumn('species_genus', 'adult_phagy_level')) {
                $table->dropColumn('adult_phagy_level');
            }
        });

        Schema::table('species_plant', function (Blueprint $table) {
            if (Schema::hasColumn('species_plant', 'larval_phagy_level')) {
                $table->dropColumn('larval_phagy_level');
            }

            if (Schema::hasColumn('species_plant', 'adult_phagy_level')) {
                $table->dropColumn('adult_phagy_level');
            }
        });
    }
};
