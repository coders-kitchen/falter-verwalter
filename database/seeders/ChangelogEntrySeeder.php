<?php

namespace Database\Seeders;

use App\Models\ChangelogEntry;
use Illuminate\Database\Seeder;

class ChangelogEntrySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'version' => '2025.11.02.1',
                'title' => 'Projektgrundlage: Admin-Basisdaten spezifiziert',
                'summary' => 'Grundlage fuer den strukturierten Aufbau und die Pflege der Falter- und Pflanzendaten wurde definiert.',
                'details' => "Admin: Spezifikation fuer Admin-Bereich und Datenpflege vorbereitet.\nQuelle: specs/001-admin-basis-daten/spec.md",
                'audience' => 'admin',
                'published_at' => '2025-11-02T17:17:01+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2025.11.02.2',
                'title' => 'Regionales Gefaehrdungsmodell neu konzipiert',
                'summary' => 'Verbreitungsgebiet und Gefaehrdungsstatus wurden als getrennte fachliche Ebenen definiert.',
                'details' => "Public: Grundlage fuer differenziertere regionale Darstellungen geschaffen.\nAdmin: Pflegeprozess fuer Regionen und Gefaehrdungsstatus fachlich getrennt.\nQuelle: specs/002-endangered-regions-model/spec.md",
                'audience' => 'both',
                'published_at' => '2025-11-02T17:17:01+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.1',
                'title' => 'Oekologische Zeigerwerte mit X und ? erweitert',
                'summary' => 'Pflanzenwerte koennen nun auch als indifferent (X) oder ungeklaert (?) erfasst werden.',
                'details' => "Public: Pflanzeninformationen koennen praeziser eingeordnet und gelesen werden.\nAdmin: Erfassungslogik fuer Zeigerwerte um zusaetzliche Zustandswerte erweitert.\nQuelle: features/feature_indicator_state_x_unknown.md",
                'audience' => 'both',
                'published_at' => '2026-02-15T16:02:41+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.2',
                'title' => 'Schwermetallresistenz bei Pflanzen ergaenzt',
                'summary' => 'Pflanzen enthalten nun einen zusaetzlichen Hinweis zur Schwermetallresistenz.',
                'details' => "Public: Weitere Pflanzeneigenschaft fuer die Einordnung verfuegbar.\nAdmin: Neuer Pflegewert fuer Schwermetallresistenz eingefuehrt.\nQuelle: features/feature_heavy_metal_resistance.md",
                'audience' => 'both',
                'published_at' => '2026-02-15T16:02:41+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.3',
                'title' => 'Gefaehrdungskategorie direkt an Pflanzen',
                'summary' => 'Pflanzen koennen direkt mit einer Gefaehrdungskategorie erfasst werden.',
                'details' => "Public: Pflanzendetails koennen Gefaehrdung besser widerspiegeln.\nAdmin: Direkte Kategorienpflege an Pflanzeneintraegen ermoeglicht.\nQuelle: features/feature_plant_threat_category.md",
                'audience' => 'both',
                'published_at' => '2026-02-15T16:10:08+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.4',
                'title' => 'Salbei-Indikator fuer Arten eingefuehrt',
                'summary' => 'Arten koennen mit einem Hinweis zur Salbei-Nutzung versehen werden.',
                'details' => "Public: Zusatzeigenschaft an Artdetails sichtbar.\nAdmin: Neues Feld zur Erfassung der Salbei-Nutzung verfuegbar.\nQuelle: features/feature_species_sage_feeding_indicator.md",
                'audience' => 'both',
                'published_at' => '2026-02-15T16:17:14+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.5',
                'title' => 'Pflanzenzuordnung von Generation auf Art verschoben',
                'summary' => 'Futter- und Nektarpflanzen werden nun zentral pro Art statt pro Generation gepflegt.',
                'details' => "Public: Treffer und Artdetails basieren auf stabileren Pflanzenbeziehungen.\nAdmin: Zuordnungspflege wurde auf Artniveau zentralisiert.\nQuelle: features/feature_species_plant_assignment.md",
                'audience' => 'both',
                'published_at' => '2026-02-15T16:41:12+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.6',
                'title' => 'Generationen werden automatisch nummeriert',
                'summary' => 'Generationen erhalten ihre Nummerierung nun automatisch.',
                'details' => "Admin: Manuelle Nummernvergabe in der Pflege entfiel, Ableitungen erfolgen automatisch.\nQuelle: features/feature_auto_generation_numbering.md",
                'audience' => 'admin',
                'published_at' => '2026-02-15T16:50:48+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.7',
                'title' => 'Bulk-Zuordnung fuer Art-Pflanzen verbessert',
                'summary' => 'Zuordnungen grosser Pflanzenmengen wurden im Admin-Workflow deutlich vereinfacht.',
                'details' => "Admin: Mehrfachauswahl und effizientere Bearbeitung fuer grosse Datenmengen.\nQuelle: features/feature_species_plant_bulk_assignment.md",
                'audience' => 'admin',
                'published_at' => '2026-02-15T17:30:46+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.8',
                'title' => 'Taxonomie auf normierte Hierarchie umgestellt',
                'summary' => 'Die taxonomische Struktur wurde vereinheitlicht und ueber alle Bereiche konsistenter gemacht.',
                'details' => "Public: Taxonomiepfade und Filter koennen konsistenter dargestellt werden.\nAdmin: Hierarchische Pflege von Familien, Unterfamilien, Triben und Gattungen strukturiert.\nQuelle: features/feature_taxonomy_normalization.md",
                'audience' => 'both',
                'published_at' => '2026-02-15T18:07:19+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.9',
                'title' => 'Oeffentliche Pflanzensuche ausgebaut',
                'summary' => 'Die Pflanzensuche im Butterfly-Finder wurde fuer grosse Datenbestaende verbessert.',
                'details' => "Public: Erweiterte Such- und Filteroptionen im Finder verfuegbar.\nAdmin: Keine direkte Pflegeaenderung, aber bessere Nutzbarkeit der gepflegten Daten.\nQuelle: features/feature_public_plant_filtering.md",
                'audience' => 'public',
                'published_at' => '2026-02-15T19:01:16+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.15.10',
                'title' => 'Regionale Karte auf GeoJSON und Leaflet umgestellt',
                'summary' => 'Die Verbreitungskarte zeigt Regionen jetzt als interaktive Flaechen statt Blockraster.',
                'details' => "Public: Deutlich praezisere und interaktive Kartendarstellung.\nAdmin: Grundlage fuer bessere Qualitaetskontrolle regionaler Daten.\nQuelle: features/feature_regional_map_geojson_leaflet.md",
                'audience' => 'public',
                'published_at' => '2026-02-15T19:41:50+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.24.1',
                'title' => 'Primaer- und Sekundaer-Praeferenz je Lebensstadium',
                'summary' => 'Pflanzenbeziehungen koennen je Raupe und Falter als primaer oder sekundaer unterschieden werden.',
                'details' => "Public: Suchtreffer werden staerker auf fachlich relevante primaere Beziehungen fokussiert.\nAdmin: Neue Praeferenzpflege je Lebensstadium in der Zuordnung verfuegbar.\nQuelle: features/feature_species_plant_primary_secondary_preference.md",
                'audience' => 'both',
                'published_at' => '2026-02-24T21:07:32+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
            [
                'version' => '2026.02.24.2',
                'title' => 'Gattungszuordnungen (sp.) zusaetzlich zu Arten',
                'summary' => 'Zuordnungen koennen nun auf Pflanzenart- oder Gattungsebene erfolgen.',
                'details' => "Public: Arteninformationen beruecksichtigen jetzt auch Gattungsbeziehungen (sp.).\nAdmin: Multi-Select-Zuordnung fuer Gattungen im gleichen Workflow wie bei Pflanzenarten.\nQuelle: features/feature_species_genus_assignment.md",
                'audience' => 'both',
                'published_at' => '2026-02-24T21:37:05+01:00',
                'is_active' => true,
                'commit_refs' => [],
            ],
        ];

        foreach ($entries as $entry) {
            ChangelogEntry::updateOrCreate(
                ['version' => $entry['version']],
                $entry
            );
        }
    }
}
