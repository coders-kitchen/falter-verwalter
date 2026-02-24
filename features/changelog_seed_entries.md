# Changelog Seed Entries (Draft)

## Purpose
Initial changelog content for the planned changelog feature.
Timestamps are based on Git file creation time (`diff-filter=A`) of feature/spec documents.

## Suggested Entries
| Published At | Audience | Title | Summary | Source |
|---|---|---|---|---|
| 2025-11-02T17:17:01+01:00 | both | Projektgrundlage: Admin-Basisdaten spezifiziert | Erste fachliche und technische Grundlage für die Erfassung und Verwaltung von Arten-, Pflanzen- und Stammdaten im Admin-Bereich. | `specs/001-admin-basis-daten/spec.md` |
| 2025-11-02T17:17:01+01:00 | both | Regionales Gefährdungsmodell neu konzipiert | Trennung von Verbreitungsgebiet und Gefährdungsstatus pro Region als Modellgrundlage definiert. | `specs/002-endangered-regions-model/spec.md` |
| 2026-02-15T16:02:41+01:00 | both | Ökologische Zeigerwerte mit `X` und `?` erweitert | Zeigerwerte unterstützen nun numerisch, indifferent (`X`) und ungeklärt (`?`) für präzisere Datenerfassung. | `features/feature_indicator_state_x_unknown.md` |
| 2026-02-15T16:02:41+01:00 | both | Schwermetallresistenz bei Pflanzen ergänzt | Neuer Indikator zur Einordnung von Pflanzen hinsichtlich Schwermetallresistenz. | `features/feature_heavy_metal_resistance.md` |
| 2026-02-15T16:10:08+01:00 | both | Gefährdungskategorie direkt an Pflanzen | Pflanzen können direkt mit einer Gefährdungskategorie versehen und ausgewertet werden. | `features/feature_plant_threat_category.md` |
| 2026-02-15T16:17:14+01:00 | both | Salbei-Indikator für Arten eingeführt | Arten erhalten ein zusätzliches Feld, ob Salbei als Futterquelle genutzt wird. | `features/feature_species_sage_feeding_indicator.md` |
| 2026-02-15T16:41:12+01:00 | both | Pflanzenzuordnung von Generation auf Art verschoben | Nektar- und Raupenfutterpflanzen werden zentral je Art statt je Generation gepflegt. | `features/feature_species_plant_assignment.md` |
| 2026-02-15T16:50:48+01:00 | both | Generationen werden automatisch nummeriert | Manuelle Generationsnummern entfallen, Ableitungen erfolgen automatisch aus den Daten. | `features/feature_auto_generation_numbering.md` |
| 2026-02-15T17:30:46+01:00 | admin | Bulk-Zuordnung für Art-Pflanzen verbessert | Admin-Workflow für große Mengen an Pflanzenzuordnungen mit Mehrfachauswahl und effizienterem Handling. | `features/feature_species_plant_bulk_assignment.md` |
| 2026-02-15T18:07:19+01:00 | both | Taxonomie auf normierte Hierarchie umgestellt | Familien/Unterfamilien/Triben/Gattungen wurden strukturell vereinheitlicht und besser filterbar gemacht. | `features/feature_taxonomy_normalization.md` |
| 2026-02-15T19:01:16+01:00 | both | Öffentliche Pflanzensuche ausgebaut | Butterfly-Finder erhielt erweiterte Pflanzenfilter für bessere Nutzbarkeit bei großen Datenbeständen. | `features/feature_public_plant_filtering.md` |
| 2026-02-15T19:41:50+01:00 | both | Regionale Karte auf GeoJSON/Leaflet umgestellt | Verbreitung wird als interaktive Polygonkarte statt Blockraster visualisiert. | `features/feature_regional_map_geojson_leaflet.md` |
| 2026-02-24T21:07:32+01:00 | both | Primär/Sekundär-Präferenz je Lebensstadium | Pflanzenbeziehungen können je Raupe/Falter als primär oder sekundär klassifiziert werden; Public-Suche nutzt primäre Beziehungen. | `features/feature_species_plant_primary_secondary_preference.md` |
| 2026-02-24T21:37:05+01:00 | both | Gattungszuordnungen (`sp.`) zusätzlich zu Arten | Arten können nun gegen konkrete Pflanzenarten oder ganze Pflanzengattungen zugeordnet werden. | `features/feature_species_genus_assignment.md` |

## Optional Next Step
When the changelog feature is implemented, convert this list into `changelog_entries` seed rows.
