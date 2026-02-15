<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $familyRows = DB::table('families')->select('id', 'user_id', 'name', 'subfamily', 'tribe', 'genus', 'type')->get();

        $baseFamilyCache = [];
        $subfamilyCache = [];
        $tribeCache = [];
        $genusCache = [];

        $ensureBaseFamily = function (string $type, string $familyName, int $userId) use (&$baseFamilyCache): int {
            $normalizedType = trim($type);
            $normalizedName = trim($familyName);
            $key = $normalizedType . '|' . mb_strtolower($normalizedName);

            if (isset($baseFamilyCache[$key])) {
                return $baseFamilyCache[$key];
            }

            $existingId = DB::table('families')
                ->where('type', $normalizedType)
                ->where('name', $normalizedName)
                ->whereNull('subfamily')
                ->whereNull('tribe')
                ->whereNull('genus')
                ->value('id');

            if ($existingId) {
                return $baseFamilyCache[$key] = (int) $existingId;
            }

            $id = DB::table('families')->insertGetId([
                'user_id' => $userId,
                'name' => $normalizedName,
                'subfamily' => null,
                'tribe' => null,
                'genus' => null,
                'type' => $normalizedType,
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $baseFamilyCache[$key] = (int) $id;
        };

        $ensureSubfamily = function (int $baseFamilyId, ?string $subfamilyName) use (&$subfamilyCache): int {
            $name = trim((string) ($subfamilyName ?: 'Ohne Unterfamilie'));
            $key = $baseFamilyId . '|' . mb_strtolower($name);

            if (isset($subfamilyCache[$key])) {
                return $subfamilyCache[$key];
            }

            $existingId = DB::table('subfamilies')
                ->where('family_id', $baseFamilyId)
                ->where('name', $name)
                ->value('id');

            if ($existingId) {
                return $subfamilyCache[$key] = (int) $existingId;
            }

            $id = DB::table('subfamilies')->insertGetId([
                'family_id' => $baseFamilyId,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $subfamilyCache[$key] = (int) $id;
        };

        $ensureTribe = function (int $subfamilyId, ?string $tribeName) use (&$tribeCache): ?int {
            $name = trim((string) ($tribeName ?? ''));
            if ($name === '') {
                return null;
            }

            $key = $subfamilyId . '|' . mb_strtolower($name);

            if (isset($tribeCache[$key])) {
                return $tribeCache[$key];
            }

            $existingId = DB::table('tribes')
                ->where('subfamily_id', $subfamilyId)
                ->where('name', $name)
                ->value('id');

            if ($existingId) {
                return $tribeCache[$key] = (int) $existingId;
            }

            $id = DB::table('tribes')->insertGetId([
                'subfamily_id' => $subfamilyId,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $tribeCache[$key] = (int) $id;
        };

        $ensureGenus = function (int $subfamilyId, ?int $tribeId, string $genusName) use (&$genusCache): int {
            $name = trim($genusName);
            $tribeKey = $tribeId === null ? 'null' : (string) $tribeId;
            $key = $subfamilyId . '|' . $tribeKey . '|' . mb_strtolower($name);

            if (isset($genusCache[$key])) {
                return $genusCache[$key];
            }

            $existingId = DB::table('genera')
                ->where('subfamily_id', $subfamilyId)
                ->where('name', $name)
                ->when($tribeId === null, fn ($q) => $q->whereNull('tribe_id'), fn ($q) => $q->where('tribe_id', $tribeId))
                ->value('id');

            if ($existingId) {
                return $genusCache[$key] = (int) $existingId;
            }

            $id = DB::table('genera')->insertGetId([
                'subfamily_id' => $subfamilyId,
                'tribe_id' => $tribeId,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $genusCache[$key] = (int) $id;
        };

        // Pre-create genera from legacy family rows where genus was explicitly stored.
        foreach ($familyRows as $familyRow) {
            $genusName = trim((string) ($familyRow->genus ?? ''));
            if ($genusName === '') {
                continue;
            }

            $baseFamilyId = $ensureBaseFamily((string) $familyRow->type, (string) $familyRow->name, (int) $familyRow->user_id);
            $subfamilyId = $ensureSubfamily($baseFamilyId, $familyRow->subfamily);
            $tribeId = $ensureTribe($subfamilyId, $familyRow->tribe);
            $ensureGenus($subfamilyId, $tribeId, $genusName);
        }

        $familyById = $familyRows->keyBy('id');

        // Backfill species.genus_id
        DB::table('species')
            ->select('id', 'family_id', 'scientific_name')
            ->orderBy('id')
            ->chunk(200, function ($rows) use ($familyById, $ensureBaseFamily, $ensureSubfamily, $ensureTribe, $ensureGenus) {
                foreach ($rows as $row) {
                    if ($row->genus_id ?? null) {
                        continue;
                    }

                    $family = $familyById->get($row->family_id);
                    if (!$family) {
                        continue;
                    }

                    $genusName = trim((string) ($family->genus ?? ''));

                    if ($genusName === '' && !empty($row->scientific_name)) {
                        $parts = preg_split('/\s+/', trim((string) $row->scientific_name));
                        $genusName = $parts[0] ?? '';
                    }

                    if ($genusName === '') {
                        continue;
                    }

                    $baseFamilyId = $ensureBaseFamily((string) $family->type, (string) $family->name, (int) $family->user_id);
                    $subfamilyId = $ensureSubfamily($baseFamilyId, $family->subfamily);
                    $tribeId = $ensureTribe($subfamilyId, $family->tribe);
                    $genusId = $ensureGenus($subfamilyId, $tribeId, $genusName);

                    DB::table('species')->where('id', $row->id)->update(['genus_id' => $genusId]);
                }
            });

        // Backfill plants.genus_id
        DB::table('plants')
            ->select('id', 'family_id', 'scientific_name', 'family_genus')
            ->orderBy('id')
            ->chunk(200, function ($rows) use ($familyById, $ensureBaseFamily, $ensureSubfamily, $ensureTribe, $ensureGenus) {
                foreach ($rows as $row) {
                    if ($row->genus_id ?? null) {
                        continue;
                    }

                    if (!$row->family_id) {
                        continue;
                    }

                    $family = $familyById->get($row->family_id);
                    if (!$family) {
                        continue;
                    }

                    $genusName = trim((string) ($family->genus ?? ''));

                    if ($genusName === '' && !empty($row->family_genus)) {
                        $genusName = trim((string) $row->family_genus);
                    }

                    if ($genusName === '' && !empty($row->scientific_name)) {
                        $parts = preg_split('/\s+/', trim((string) $row->scientific_name));
                        $genusName = $parts[0] ?? '';
                    }

                    if ($genusName === '') {
                        continue;
                    }

                    $baseFamilyId = $ensureBaseFamily((string) $family->type, (string) $family->name, (int) $family->user_id);
                    $subfamilyId = $ensureSubfamily($baseFamilyId, $family->subfamily);
                    $tribeId = $ensureTribe($subfamilyId, $family->tribe);
                    $genusId = $ensureGenus($subfamilyId, $tribeId, $genusName);

                    DB::table('plants')->where('id', $row->id)->update(['genus_id' => $genusId]);
                }
            });
    }

    public function down(): void
    {
        DB::table('species')->update(['genus_id' => null]);
        DB::table('plants')->update(['genus_id' => null]);

        DB::table('genera')->delete();
        DB::table('tribes')->delete();
        DB::table('subfamilies')->delete();
    }
};
