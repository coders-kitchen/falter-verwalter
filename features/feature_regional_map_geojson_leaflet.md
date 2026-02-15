# Feature: Regional Distribution Map with GeoJSON (Leaflet)

## Goal
Die öffentliche Verbreitungskarte zeigt Gebiete nicht mehr als Block-Grid, sondern als echte interaktive Karte auf Basis von GeoJSON-Polygonen.

## User Flow
- Admin pflegt Verbreitungsgebiete inklusive stabiler Kennung (`code`) und optionaler GeoJSON-Geometrie.
- Besucher öffnen `/map` und sehen die Gebiete als Flächen auf einer Leaflet-Karte.
- Besucher wechseln zwischen `Alle Arten` und `Gefährdete Arten`.
- Die Flächenfarbe bleibt nach Häufigkeit (Heatmap-Logik) abgestuft.

## Data Model
`distribution_areas` wurde erweitert um:
- `code` (stabile, eindeutige Kennung)
- `geometry_geojson` (GeoJSON `Polygon`/`MultiPolygon`)

Migration:
- `database/migrations/2026_02_16_002000_add_code_and_geometry_to_distribution_areas_table.php`

Hinweise:
- Bestehende Datensätze werden mit slug-basierten Codes backfilled.
- Geometrie bleibt optional, damit bestehende Gebiete ohne Polygon weiter bestehen können.

## Backend Changes
- `app/Models/DistributionArea.php`
  - `code` und `geometry_geojson` in `$fillable`
  - `geometry_geojson` als Array-Cast
- `app/Livewire/Public/RegionalDistributionMap.php`
  - baut zusätzlich ein GeoJSON FeatureCollection-Payload auf
  - liefert Farbwerte pro Gebiet für die Kartenfläche
  - dispatcht Browser-Event bei Moduswechsel
- `app/Livewire/DistributionAreaManager.php`
  - Admin-Form um `code` und `geometry_geojson` erweitert
  - Validierung für `code` und GeoJSON JSON-String
- `app/Http/Requests/DistributionAreaRequest.php`
  - API-Validierung um `code` und `geometry_geojson` ergänzt
- `app/Http/Controllers/DistributionAreaController.php`
  - dekodiert `geometry_geojson` vor Persistenz
- `app/Http/Resources/DistributionAreaResource.php`
  - gibt `code` und `geometry_geojson` aus
- `database/seeders/DistributionAreaSeeder.php`
  - ergänzt feste Codes für Seed-Daten

## UI Changes
- `resources/views/livewire/public/regional-distribution-map.blade.php`
  - ersetzt Grid-Kachelansicht durch Leaflet-Kartenansicht
  - zeichnet GeoJSON-Flächen farbcodiert
  - Popup pro Gebiet mit Name, Code, Anzahl
  - Warnt bei Gebieten ohne Geometrie
- `resources/views/livewire/distribution-area-manager.blade.php`
  - neue Felder für `code` und GeoJSON
  - Spalte mit Geometrie-Status

## Acceptance Criteria
- `/map` zeigt eine interaktive Karte mit GeoJSON-Gebieten.
- Gebiete mit Geometrie erscheinen als Polygone/MultiPolygone.
- Farbintensität pro Gebiet entspricht der bestehenden Count-Logik.
- Admin kann Code und GeoJSON je Gebiet pflegen.
- API akzeptiert und liefert Code + Geometrie.
- Gebiete ohne Geometrie werden weiterhin gelistet und im UI klar markiert.

## Rollout
1. `php artisan migrate`
2. GeoJSON pro Gebiet in Adminmaske pflegen.
3. Karte auf `/map` validieren.
