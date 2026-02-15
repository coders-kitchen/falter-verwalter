# Feature: Regional Distribution Map with GeoJSON (Leaflet)

## Goal
Die öffentliche Verbreitungskarte zeigt Gebiete nicht mehr als Block-Grid, sondern als echte interaktive Karte auf Basis von GeoJSON-Polygonen.

## User Flow
- Admin pflegt Verbreitungsgebiete inklusive stabiler Kennung (`code`) und optionaler GeoJSON-Datei.
- Besucher öffnen `/map` und sehen die Gebiete als Flächen auf einer Leaflet-Karte.
- Besucher wechseln zwischen `Alle Arten` und `Gefährdete Arten`.
- Die Flächenfarbe bleibt nach Häufigkeit (Heatmap-Logik) abgestuft.
- In der Art-Detailseite (`/species/{id}`) gibt es einen extra Tab `🗺️ Karte`.
- Dort wird die Karte nach Gefährdungscode (`threatCategory.color_code`) der gewählten Art eingefärbt.

## Data Model
`distribution_areas` wurde erweitert um:
- `code` (stabile, eindeutige Kennung)
- `geojson_path` (Pfad zur GeoJSON-Datei in `storage/app/public`)

Migration:
- `database/migrations/2026_02_16_002000_add_code_and_geometry_to_distribution_areas_table.php`
- `database/migrations/2026_02_16_003000_add_geojson_path_to_distribution_areas_table.php`
- `database/migrations/2026_02_16_004000_drop_geometry_geojson_from_distribution_areas_table.php`

Hinweise:
- Bestehende Datensätze werden mit slug-basierten Codes backfilled.
- GeoJSON wird ausschließlich dateibasiert per Upload gepflegt.

## Backend Changes
- `app/Models/DistributionArea.php`
  - `code` und `geojson_path` in `$fillable`
- `app/Livewire/Public/RegionalDistributionMap.php`
  - lädt Geometrie aus `geojson_path` (Storage-Datei)
  - baut daraus ein GeoJSON FeatureCollection-Payload auf
  - unterstützt zwei Farbmodi:
    - `count`: Heatmap-Farbe nach Anzahl
    - `threat`: Farbe nach Gefährdungscode je Gebiet
  - dispatcht Browser-Event bei Moduswechsel
- `resources/views/public/species-detail.blade.php`
  - rendert Detailinhalt via Blade-Include, damit die Seite stabil mit eingebetteten Karten-Komponenten funktioniert
- `app/Livewire/DistributionAreaManager.php`
  - Admin-Form um `code` und dateibasierten GeoJSON-Upload erweitert
  - Validierung für `code` und GeoJSON-Datei-Upload
  - erlaubt GeoJSON `Polygon`/`MultiPolygon`, `Feature` und `FeatureCollection` (mit genau einem Feature)
  - Datei-Upload bis 5 MB, Speichern unter `distribution-areas/{code}.geojson`
- `app/Http/Requests/DistributionAreaRequest.php`
  - API-Validierung um `code` und `geojson_path` ergänzt
- `app/Http/Controllers/DistributionAreaController.php`
  - listet nur notwendige Spalten inkl. `geojson_path`
- `app/Http/Resources/DistributionAreaResource.php`
  - gibt `code`, `geojson_path` und `geojson_url` aus
- `database/seeders/DistributionAreaSeeder.php`
  - ergänzt feste Codes für Seed-Daten

## UI Changes
- `resources/views/livewire/public/regional-distribution-map.blade.php`
  - ersetzt Grid-Kachelansicht durch Leaflet-Kartenansicht (map-first Layout)
  - zeichnet GeoJSON-Flächen farbcodiert
  - Popup pro Gebiet mit Name, Code, Anzahl und optionalem Threat-Status
  - Warnt bei Gebieten ohne Geometrie
- `resources/views/livewire/public/species-detail.blade.php`
  - neuer Tab `🗺️ Karte`
  - bindet `public.regional-distribution-map` mit `colorMode=threat` ein
- `resources/views/livewire/distribution-area-manager.blade.php`
  - neue Felder für `code` und GeoJSON-Datei-Upload
  - Spalte mit Geometrie-Status

## Acceptance Criteria
- `/map` zeigt eine interaktive Karte mit GeoJSON-Gebieten.
- Gebiete mit Geometrie erscheinen als Polygone/MultiPolygone.
- Farbintensität pro Gebiet entspricht der bestehenden Count-Logik.
- `/species/{id}` enthält einen Karten-Tab mit artbezogener Färbung nach Gefährdungscode.
- Admin kann Code und GeoJSON je Gebiet pflegen.
- Admin kann große GeoJSON-Polygone per Datei-Upload pflegen.
- API liefert Code + Datei-Referenz.
- Gebiete ohne Geometrie werden weiterhin gelistet und im UI klar markiert.

## Rollout
1. `php artisan migrate`
2. GeoJSON pro Gebiet in Adminmaske pflegen.
3. Karte auf `/map` validieren.
