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
            if (!Schema::hasColumn('species_plant', 'is_nectar')) {
                $table->boolean('is_nectar')->default(false)->after('plant_id');
            }
            if (!Schema::hasColumn('species_plant', 'is_larval_host')) {
                $table->boolean('is_larval_host')->default(false)->after('is_nectar');
            }
        });

        // Migrate existing species_plant enum usage into boolean flags.
        if (Schema::hasColumn('species_plant', 'plant_type')) {
            DB::table('species_plant')
                ->where('plant_type', 'nectar')
                ->update(['is_nectar' => true]);

            DB::table('species_plant')
                ->where('plant_type', 'host')
                ->update(['is_larval_host' => true]);
        }

        // Build a duplicate-safe aggregate map from generation JSON arrays.
        $entries = [];

        DB::table('generations')
            ->select('species_id', 'nectar_plants', 'larval_host_plants')
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$entries) {
                foreach ($rows as $row) {
                    $speciesId = (int) $row->species_id;

                    $nectarPlants = json_decode($row->nectar_plants ?? '[]', true);
                    $larvalPlants = json_decode($row->larval_host_plants ?? '[]', true);

                    if (!is_array($nectarPlants)) {
                        $nectarPlants = [];
                    }
                    if (!is_array($larvalPlants)) {
                        $larvalPlants = [];
                    }

                    foreach ($nectarPlants as $plantId) {
                        $plantId = (int) $plantId;
                        if ($plantId <= 0) {
                            continue;
                        }

                        $key = $speciesId . ':' . $plantId;
                        if (!isset($entries[$key])) {
                            $entries[$key] = [
                                'species_id' => $speciesId,
                                'plant_id' => $plantId,
                                'is_nectar' => false,
                                'is_larval_host' => false,
                            ];
                        }

                        $entries[$key]['is_nectar'] = true;
                    }

                    foreach ($larvalPlants as $plantId) {
                        $plantId = (int) $plantId;
                        if ($plantId <= 0) {
                            continue;
                        }

                        $key = $speciesId . ':' . $plantId;
                        if (!isset($entries[$key])) {
                            $entries[$key] = [
                                'species_id' => $speciesId,
                                'plant_id' => $plantId,
                                'is_nectar' => false,
                                'is_larval_host' => false,
                            ];
                        }

                        $entries[$key]['is_larval_host'] = true;
                    }
                }
            });

        // Merge in existing pivot rows so no previous meaning is overwritten.
        DB::table('species_plant')
            ->select('species_id', 'plant_id', 'is_nectar', 'is_larval_host')
            ->orderBy('id')
            ->chunk(200, function ($rows) use (&$entries) {
                foreach ($rows as $row) {
                    $speciesId = (int) $row->species_id;
                    $plantId = (int) $row->plant_id;
                    $key = $speciesId . ':' . $plantId;

                    if (!isset($entries[$key])) {
                        $entries[$key] = [
                            'species_id' => $speciesId,
                            'plant_id' => $plantId,
                            'is_nectar' => false,
                            'is_larval_host' => false,
                        ];
                    }

                    $entries[$key]['is_nectar'] = $entries[$key]['is_nectar'] || (bool) $row->is_nectar;
                    $entries[$key]['is_larval_host'] = $entries[$key]['is_larval_host'] || (bool) $row->is_larval_host;
                }
            });

        if (!empty($entries)) {
            $now = now();
            $upsertRows = array_map(function (array $entry) use ($now) {
                return [
                    'species_id' => $entry['species_id'],
                    'plant_id' => $entry['plant_id'],
                    'is_nectar' => $entry['is_nectar'],
                    'is_larval_host' => $entry['is_larval_host'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, array_values($entries));

            DB::table('species_plant')->upsert(
                $upsertRows,
                ['species_id', 'plant_id'],
                ['is_nectar', 'is_larval_host', 'updated_at']
            );
        }

        // Ensure every existing mapping keeps at least one semantic meaning.
        DB::table('species_plant')
            ->where('is_nectar', false)
            ->where('is_larval_host', false)
            ->update(['is_larval_host' => true]);

        if (Schema::hasColumn('species_plant', 'plant_type')) {
            Schema::table('species_plant', function (Blueprint $table) {
                $table->dropColumn('plant_type');
            });
        }
    }

    public function down(): void
    {
        Schema::table('species_plant', function (Blueprint $table) {
            if (!Schema::hasColumn('species_plant', 'plant_type')) {
                $table->enum('plant_type', ['host', 'nectar', 'other'])->default('host')->after('plant_id');
            }
        });

        DB::table('species_plant')->update([
            'plant_type' => DB::raw("CASE WHEN is_nectar = 1 THEN 'nectar' ELSE 'host' END"),
        ]);

        Schema::table('species_plant', function (Blueprint $table) {
            if (Schema::hasColumn('species_plant', 'is_nectar')) {
                $table->dropColumn('is_nectar');
            }
            if (Schema::hasColumn('species_plant', 'is_larval_host')) {
                $table->dropColumn('is_larval_host');
            }
        });
    }
};
