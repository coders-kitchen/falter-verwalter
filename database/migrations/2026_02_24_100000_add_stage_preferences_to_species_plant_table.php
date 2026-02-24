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
            if (!Schema::hasColumn('species_plant', 'adult_preference')) {
                $table->enum('adult_preference', ['primaer', 'sekundaer'])
                    ->nullable()
                    ->after('is_larval_host');
            }

            if (!Schema::hasColumn('species_plant', 'larval_preference')) {
                $table->enum('larval_preference', ['primaer', 'sekundaer'])
                    ->nullable()
                    ->after('adult_preference');
            }
        });

        DB::table('species_plant')
            ->where('is_nectar', true)
            ->whereNull('adult_preference')
            ->update(['adult_preference' => 'primaer']);

        DB::table('species_plant')
            ->where('is_larval_host', true)
            ->whereNull('larval_preference')
            ->update(['larval_preference' => 'primaer']);
    }

    public function down(): void
    {
        Schema::table('species_plant', function (Blueprint $table) {
            if (Schema::hasColumn('species_plant', 'larval_preference')) {
                $table->dropColumn('larval_preference');
            }

            if (Schema::hasColumn('species_plant', 'adult_preference')) {
                $table->dropColumn('adult_preference');
            }
        });
    }
};
