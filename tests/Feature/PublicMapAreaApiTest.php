<?php

use App\Models\DistributionArea;
use App\Models\Family;
use App\Models\Species;
use App\Models\ThreatCategory;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function createPublicMapFixture(): array
{
    $user = User::factory()->create();

    $family = Family::create([
        'user_id' => $user->id,
        'name' => 'Nymphalidae',
        'type' => 'butterfly',
    ]);

    $species = Species::create([
        'user_id' => $user->id,
        'family_id' => $family->id,
        'name' => 'Distelfalter',
        'scientific_name' => 'Vanessa cardui',
        'size_category' => 'M',
    ]);

    $area = DistributionArea::create([
        'user_id' => $user->id,
        'name' => 'Niedersachsen West',
        'code' => 'niedersachsen-west',
        'geojson_path' => 'distribution-areas/niedersachsen-west.geojson',
    ]);

    $threatCategory = ThreatCategory::create([
        'user_id' => $user->id,
        'code' => 'VU',
        'label' => 'Gefaehrdet',
        'description' => 'Gefaehrdet',
        'rank' => 1,
        'color_code' => '#f59e0b',
    ]);

    return compact('user', 'family', 'species', 'area', 'threatCategory');
}

test('public map meta endpoint returns general area meta', function () {
    $fixture = createPublicMapFixture();

    $fixture['species']->distributionAreas()->attach($fixture['area']->id, [
        'status' => 'heimisch',
        'user_id' => $fixture['user']->id,
    ]);

    $response = $this->getJson('/api/map/areas/niedersachsen-west/meta');

    $response->assertOk()
        ->assertJson([
            'data' => [
                'code' => 'niedersachsen-west',
                'name' => 'Niedersachsen West',
                'species_distribution_area_count' => 1,
            ],
        ]);
});

test('public map meta endpoint returns species specific threat status', function () {
    $fixture = createPublicMapFixture();

    $fixture['species']->distributionAreas()->attach($fixture['area']->id, [
        'status' => 'heimisch',
        'threat_category_id' => $fixture['threatCategory']->id,
        'user_id' => $fixture['user']->id,
    ]);

    $response = $this->getJson('/api/map/areas/niedersachsen-west/meta?species_id=' . $fixture['species']->id);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'code' => 'niedersachsen-west',
                'name' => 'Niedersachsen West',
                'species' => [
                    'id' => $fixture['species']->id,
                    'threat_status' => [
                        'code' => 'VU',
                        'label' => 'Gefaehrdet',
                        'color' => '#f59e0b',
                    ],
                ],
            ],
        ]);
});

test('public map meta endpoint returns species null when no species specific mapping exists', function () {
    $fixture = createPublicMapFixture();

    $response = $this->getJson('/api/map/areas/niedersachsen-west/meta?species_id=' . $fixture['species']->id);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'code' => 'niedersachsen-west',
                'name' => 'Niedersachsen West',
                'species' => null,
            ],
        ]);
});

test('public map meta endpoint validates species id', function () {
    $fixture = createPublicMapFixture();

    $response = $this->getJson('/api/map/areas/niedersachsen-west/meta?species_id=999999');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['species_id']);
});

test('public map geometry endpoint returns polygon geometry and etag headers', function () {
    Storage::fake('public');
    $fixture = createPublicMapFixture();

    Storage::disk('public')->put($fixture['area']->geojson_path, json_encode([
        'type' => 'Feature',
        'geometry' => [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [8.1, 52.4],
                    [8.2, 52.5],
                    [8.3, 52.4],
                    [8.1, 52.4],
                ],
            ],
        ],
        'properties' => [],
    ]));

    $response = $this->getJson('/api/map/areas/niedersachsen-west/geometry');

    $response->assertOk()
        ->assertHeader('ETag')
        ->assertHeader('Last-Modified')
        ->assertJson([
            'data' => [
                'code' => 'niedersachsen-west',
                'geometry' => [
                    'type' => 'Polygon',
                ],
            ],
        ]);
});

test('public map geometry endpoint returns not modified when etag matches', function () {
    Storage::fake('public');
    $fixture = createPublicMapFixture();

    $payload = json_encode([
        'type' => 'Feature',
        'geometry' => [
            'type' => 'Polygon',
            'coordinates' => [
                [
                    [8.1, 52.4],
                    [8.2, 52.5],
                    [8.3, 52.4],
                    [8.1, 52.4],
                ],
            ],
        ],
        'properties' => [],
    ]);

    Storage::disk('public')->put($fixture['area']->geojson_path, $payload);
    $etag = '"' . sha1($payload) . '"';

    $response = $this->withHeaders([
        'If-None-Match' => $etag,
    ])->get('/api/map/areas/niedersachsen-west/geometry');

    $response->assertStatus(304);
});
