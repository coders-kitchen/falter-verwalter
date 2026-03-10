# Feature: Lebensraum-Filter in der öffentlichen Pflanzensuche

## Goal
Normale Nutzer sollen die Pflanzenauswahl im öffentlichen Butterfly-Finder zusätzlich nach Lebensräumen eingrenzen können.

## Umsetzung
- Neue Filter-Property `filterHabitatIds` in `app/Livewire/Public/PlantButterflyFinder.php`
- Persistenz des Filters per Query-String
- Pflanzenliste filtert per `whereHas('habitats')` auf die gewählten Lebensräume
- Erweiterte Filter im UI um Mehrfachauswahl `Lebensräume` ergänzt

## Acceptance
- Endnutzer kann im Bereich `Erweiterte Filter` einen oder mehrere Lebensräume auswählen.
- Die sichtbare Pflanzenliste wird sofort auf passende Pflanzen eingeschränkt.
- Bereits ausgewählte Pflanzen bleiben weiterhin als Auswahl-Badges sichtbar.
