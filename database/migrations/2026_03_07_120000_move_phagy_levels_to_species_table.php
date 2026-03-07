<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PHAGY_ORDER = [
        'unbekannt' => 1,
        'monophag' => 2,
        'oligophag' => 3,
        'polyphag' => 4,
    ];

    public function up(): void
    {
        Schema::table('species', function (Blueprint $table) {
            if (!Schema::hasColumn('species', 'adult_phagy_level')) {
                $table->enum('adult_phagy_level', ['unbekannt', 'monophag', 'oligophag', 'polyphag'])
                    ->nullable()
                    ->after('hibernation_stage');
            }

            if (!Schema::hasColumn('species', 'larval_phagy_level')) {
                $table->enum('larval_phagy_level', ['unbekannt', 'monophag', 'oligophag', 'polyphag'])
                    ->nullable()
                    ->after('adult_phagy_level');
            }
        });

        $aggregatedLevels = [];

        $collect = function (string $table) use (&$aggregatedLevels): void {
            DB::table($table)
                ->select('species_id', 'adult_phagy_level', 'larval_phagy_level')
                ->orderBy('species_id')
                ->chunk(1000, function ($rows) use (&$aggregatedLevels): void {
                    foreach ($rows as $row) {
                        $speciesId = (int) $row->species_id;

                        if (!isset($aggregatedLevels[$speciesId])) {
                            $aggregatedLevels[$speciesId] = [
                                'adult' => null,
                                'larval' => null,
                            ];
                        }

                        $aggregatedLevels[$speciesId]['adult'] = $this->pickHigher(
                            $aggregatedLevels[$speciesId]['adult'],
                            $row->adult_phagy_level
                        );

                        $aggregatedLevels[$speciesId]['larval'] = $this->pickHigher(
                            $aggregatedLevels[$speciesId]['larval'],
                            $row->larval_phagy_level
                        );
                    }
                });
        };

        if (Schema::hasTable('species_plant')) {
            $collect('species_plant');
        }

        if (Schema::hasTable('species_genus')) {
            $collect('species_genus');
        }

        if (empty($aggregatedLevels)) {
            return;
        }

        foreach ($aggregatedLevels as $speciesId => $levels) {
            if ($levels['adult'] === null && $levels['larval'] === null) {
                continue;
            }

            DB::table('species')
                ->where('id', $speciesId)
                ->update([
                    'adult_phagy_level' => $levels['adult'],
                    'larval_phagy_level' => $levels['larval'],
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('species', function (Blueprint $table) {
            if (Schema::hasColumn('species', 'larval_phagy_level')) {
                $table->dropColumn('larval_phagy_level');
            }

            if (Schema::hasColumn('species', 'adult_phagy_level')) {
                $table->dropColumn('adult_phagy_level');
            }
        });
    }

    private function pickHigher(?string $current, ?string $candidate): ?string
    {
        $normalizedCandidate = $candidate === 'oglio' ? 'oligophag' : $candidate;
        $normalizedCurrent = $current === 'oglio' ? 'oligophag' : $current;

        if ($normalizedCandidate === null || $normalizedCandidate === '' || !isset(self::PHAGY_ORDER[$normalizedCandidate])) {
            return $current;
        }

        if ($normalizedCurrent === null || !isset(self::PHAGY_ORDER[$normalizedCurrent])) {
            return $normalizedCandidate;
        }

        return self::PHAGY_ORDER[$normalizedCandidate] > self::PHAGY_ORDER[$normalizedCurrent]
            ? $normalizedCandidate
            : $normalizedCurrent;
    }
};
