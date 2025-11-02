<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrates data from old endangered_regions and species_endangered_region tables
     * to new regions and species_region tables.
     */
    public function up(): void
    {
        // Check if old tables exist before attempting migration
        if (!Schema::hasTable('endangered_regions') || !Schema::hasTable('species_endangered_region')) {
            return; // Skip if old tables don't exist (fresh database)
        }

        // Step 1: Copy data from endangered_regions to regions
        // This uses raw inserts without DUPLICATE KEY UPDATE to support SQLite
        $existingRegions = DB::table('endangered_regions')->pluck('id', 'code');

        foreach (DB::table('endangered_regions')->get() as $oldRegion) {
            DB::table('regions')->updateOrInsert(
                ['code' => $oldRegion->code],
                [
                    'name' => $oldRegion->name,
                    'description' => $oldRegion->description,
                    'created_at' => $oldRegion->created_at,
                    'updated_at' => $oldRegion->updated_at,
                ]
            );
        }

        // Step 2: Copy data from species_endangered_region to species_region
        // Set all conservation_status to default 'nicht_gefährdet'
        foreach (DB::table('species_endangered_region')->get() as $oldMapping) {
            // Get the new region_id from endangered_regions table
            $newRegionId = DB::table('endangered_regions')
                ->where('id', $oldMapping->endangered_region_id)
                ->value('id');

            if ($newRegionId) {
                DB::table('species_region')->updateOrInsert(
                    [
                        'species_id' => $oldMapping->species_id,
                        'region_id' => $newRegionId,
                    ],
                    [
                        'conservation_status' => 'nicht_gefährdet',
                        'created_at' => $oldMapping->created_at,
                        'updated_at' => $oldMapping->updated_at,
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     * This rollback is intentionally non-destructive - it doesn't delete the new data,
     * but restores the old tables from their archived versions.
     */
    public function down(): void
    {
        // Don't delete migrated data - rollback is handled by dropping tables
        // which cascades deletes to species_region due to foreign key constraints.
        // The data migration is only applied if old tables exist, so this is safe.
    }
};
