<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('taxonomy:verify {--sample=10}', function () {
    $sample = max(1, (int) $this->option('sample'));

    $speciesTotal = DB::table('species')->count();
    $plantsTotal = DB::table('plants')->count();

    $speciesMissingGenus = DB::table('species')->whereNull('genus_id')->count();
    $plantsMissingGenus = DB::table('plants')->whereNull('genus_id')->count();

    $speciesWrongTypeCount = DB::table('species')
        ->join('genera', 'genera.id', '=', 'species.genus_id')
        ->join('subfamilies', 'subfamilies.id', '=', 'genera.subfamily_id')
        ->join('families', 'families.id', '=', 'subfamilies.family_id')
        ->where('families.type', '!=', 'butterfly')
        ->count();

    $plantsWrongTypeCount = DB::table('plants')
        ->join('genera', 'genera.id', '=', 'plants.genus_id')
        ->join('subfamilies', 'subfamilies.id', '=', 'genera.subfamily_id')
        ->join('families', 'families.id', '=', 'subfamilies.family_id')
        ->where('families.type', '!=', 'plant')
        ->count();

    $speciesFamilyMismatchCount = DB::table('species')
        ->join('genera', 'genera.id', '=', 'species.genus_id')
        ->join('subfamilies', 'subfamilies.id', '=', 'genera.subfamily_id')
        ->whereNotNull('species.family_id')
        ->whereColumn('species.family_id', '!=', 'subfamilies.family_id')
        ->count();

    $plantsFamilyMismatchCount = DB::table('plants')
        ->join('genera', 'genera.id', '=', 'plants.genus_id')
        ->join('subfamilies', 'subfamilies.id', '=', 'genera.subfamily_id')
        ->whereNotNull('plants.family_id')
        ->whereColumn('plants.family_id', '!=', 'subfamilies.family_id')
        ->count();

    $this->info('Taxonomy Verification Summary');
    $this->table(
        ['Metric', 'Value'],
        [
            ['species total', $speciesTotal],
            ['species missing genus_id', $speciesMissingGenus],
            ['species wrong taxonomy type (!= butterfly)', $speciesWrongTypeCount],
            ['species family mismatch vs genus hierarchy', $speciesFamilyMismatchCount],
            ['plants total', $plantsTotal],
            ['plants missing genus_id', $plantsMissingGenus],
            ['plants wrong taxonomy type (!= plant)', $plantsWrongTypeCount],
            ['plants family mismatch vs genus hierarchy', $plantsFamilyMismatchCount],
            ['taxonomy families', DB::table('families')->whereIn('type', ['plant', 'butterfly'])->count()],
            ['taxonomy subfamilies', DB::table('subfamilies')->count()],
            ['taxonomy tribes', DB::table('tribes')->count()],
            ['taxonomy genera', DB::table('genera')->count()],
        ]
    );

    $speciesMissingRows = DB::table('species')
        ->select('id', 'name', 'scientific_name', 'family_id')
        ->whereNull('genus_id')
        ->orderBy('id')
        ->limit($sample)
        ->get();

    if ($speciesMissingRows->isNotEmpty()) {
        $this->warn("Sample: species without genus_id (first {$sample})");
        $this->table(['id', 'name', 'scientific_name', 'family_id'], $speciesMissingRows->map(fn ($r) => (array) $r));
    }

    $plantsMissingRows = DB::table('plants')
        ->select('id', 'name', 'scientific_name', 'family_id')
        ->whereNull('genus_id')
        ->orderBy('id')
        ->limit($sample)
        ->get();

    if ($plantsMissingRows->isNotEmpty()) {
        $this->warn("Sample: plants without genus_id (first {$sample})");
        $this->table(['id', 'name', 'scientific_name', 'family_id'], $plantsMissingRows->map(fn ($r) => (array) $r));
    }

    $speciesTypeMismatchRows = DB::table('species')
        ->join('genera', 'genera.id', '=', 'species.genus_id')
        ->join('subfamilies', 'subfamilies.id', '=', 'genera.subfamily_id')
        ->join('families', 'families.id', '=', 'subfamilies.family_id')
        ->select('species.id', 'species.name', 'species.scientific_name', 'families.type as resolved_type')
        ->where('families.type', '!=', 'butterfly')
        ->orderBy('species.id')
        ->limit($sample)
        ->get();

    if ($speciesTypeMismatchRows->isNotEmpty()) {
        $this->warn("Sample: species with non-butterfly resolved taxonomy (first {$sample})");
        $this->table(['id', 'name', 'scientific_name', 'resolved_type'], $speciesTypeMismatchRows->map(fn ($r) => (array) $r));
    }

    $plantsTypeMismatchRows = DB::table('plants')
        ->join('genera', 'genera.id', '=', 'plants.genus_id')
        ->join('subfamilies', 'subfamilies.id', '=', 'genera.subfamily_id')
        ->join('families', 'families.id', '=', 'subfamilies.family_id')
        ->select('plants.id', 'plants.name', 'plants.scientific_name', 'families.type as resolved_type')
        ->where('families.type', '!=', 'plant')
        ->orderBy('plants.id')
        ->limit($sample)
        ->get();

    if ($plantsTypeMismatchRows->isNotEmpty()) {
        $this->warn("Sample: plants with non-plant resolved taxonomy (first {$sample})");
        $this->table(['id', 'name', 'scientific_name', 'resolved_type'], $plantsTypeMismatchRows->map(fn ($r) => (array) $r));
    }

    $hasIssues = $speciesMissingGenus > 0
        || $plantsMissingGenus > 0
        || $speciesWrongTypeCount > 0
        || $plantsWrongTypeCount > 0
        || $speciesFamilyMismatchCount > 0
        || $plantsFamilyMismatchCount > 0;

    if ($hasIssues) {
        $this->error('Taxonomy verification finished with findings.');
        return 1;
    }

    $this->info('Taxonomy verification passed without findings.');
    return 0;
})->purpose('Verify taxonomy backfill consistency for species and plants');
