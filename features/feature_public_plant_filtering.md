# Feature: Erweiterte Pflanzensuche im öffentlichen Butterfly-Finder

## Goal
Die Suche nach Schmetterlingen über Gartenpflanzen soll für große Pflanzenbestände besser nutzbar werden.

## User Feedback
- Eine reine, lange Pflanzenliste ist bei mehreren hundert Pflanzen unpraktisch.
- Gewünscht sind zusätzliche Filter (Name + Merkmale wie Zeigerwerte, Größe, Blühmonate).
- Die erweiterten Filter sollen nicht dauerhaft offen sein und auf Smartphones gut funktionieren.

## Umsetzung
- Sichtbarer Basisfilter:
  - Pflanzensuche per Name (`plantSearch`).
- Einklappbarer Bereich `Erweiterte Filter` mit zwei Untergruppen:
  - `Blühzeit & Größe`
    - Blühmonat
    - Mindesthöhe (cm)
    - Maximalhöhe (cm)
  - `Ökologische Zeigerwerte`
    - Lichtzahl, Salzzahl, Temperaturzahl, Kontinentalitätszahl,
      Reaktionszahl, Feuchtezahl, Feuchtewechsel, Stickstoffzahl
    - je Wert mit Optionen: `Alle`, `X`, `?`, numerische Ausprägungen
- Anzahl gefilterter Pflanzen wird an der Auswahl angezeigt.
- Bestehender Mehrfachauswahl-Flow bleibt erhalten.
- Ausgewählte Pflanzen bleiben als Badges sichtbar und einzeln entfernbar.

## Technische Details
- Komponente: `app/Livewire/Public/PlantButterflyFinder.php`
  - neue Filter-Properties + `queryString`-Persistenz
  - Filterlogik für:
    - Name (`LIKE`)
    - Blühmonat inklusive überjähriger Blühbereiche (z. B. Start > Ende)
    - Höhenbereich (Überlappung von `plant_height_cm_from/until`)
    - Zeigerwert-Filter mit Zustand (`numeric`, `x`, `unknown`)
  - neue Aktion: `resetPlantFilters()`
- View: `resources/views/livewire/public/plant-butterfly-finder.blade.php`
  - strukturierte, einklappbare Filter-Segmente
  - mobilfreundliches Grid-Layout
  - keine DB-Abfragen mehr pro ausgewähltem Badge (verwendet `selectedPlants` aus der Komponente)

## Acceptance
- Endnutzer kann Pflanzen nach Name filtern.
- Erweiterte Filter sind standardmäßig eingeklappt.
- Filter sind in Gruppen organisiert und mobil gut bedienbar.
- Auswahl und Anzeige gefundener Schmetterlinge funktionieren weiterhin unverändert.
