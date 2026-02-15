<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $countsBySpecies = DB::table('generations')
            ->select('species_id', DB::raw('COUNT(*) as generation_count'))
            ->groupBy('species_id')
            ->pluck('generation_count', 'species_id');

        DB::table('species')->update(['generations_per_year' => 0]);

        foreach ($countsBySpecies as $speciesId => $count) {
            DB::table('species')
                ->where('id', (int) $speciesId)
                ->update(['generations_per_year' => (int) $count]);
        }
    }

    public function down(): void
    {
        // no-op
    }
};
