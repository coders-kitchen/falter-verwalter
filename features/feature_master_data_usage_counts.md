# Feature: Nutzungszaehler fuer loeschkritische Stammdaten

## Goal
Admins sollen vor dem versehentlichen Loeschen verwendeter Stammdaten geschuetzt werden, indem in den jeweiligen Admin-Tabellen direkt sichtbar ist, wie oft ein Eintrag aktuell genutzt wird.

## Ausgangslage
- Einzelne Stammdaten koennen bereits nicht geloescht werden, wenn noch Referenzen bestehen.
- Diese Schutzlogik greift aber erst beim Loeschversuch.
- Bei Familien gibt es bereits ein aehnliches Muster: Die Tabelle zeigt die Anzahl verknuepfter Arten bzw. Pflanzen an.

## Problem
- Admins sehen vor dem Klick auf `Loeschen` nicht, ob ein Stammdatensatz noch aktiv verwendet wird.
- Das fuehrt zu unnötigen Fehlversuchen und macht die Pflege unuebersichtlich.
- Besonders relevant ist das fuer gemeinsam genutzte Kataloge wie z. B. Verbreitungsgebiete, Tags, Lebensraeume oder weitere Basisdaten mit Referenzen.

## Vorschlag
- In jeder relevanten Stammdaten-Tabelle wird eine zusaetzliche Spalte `Nutzung` angezeigt.
- Die Spalte zeigt die Summe aller fachlich relevanten Verwendungen des Eintrags.
- Eintraege ohne Nutzung zeigen `0`.
- Eintraege mit Nutzung > `0` zeigen einen sichtbaren Badge, analog zum bestehenden Familien-Muster.
- Optional kann der Loeschen-Button bei Nutzung > `0` visuell abgeschwaecht oder mit Tooltip versehen werden; die serverseitige Schutzlogik bleibt trotzdem verbindlich.

## Betroffene Bereiche
- Verbreitungsgebiete
  - Nutzung durch Artenzuordnungen in `species_distribution_areas`
- Tags
  - Nutzung durch Artenzuordnungen in `species_tag`
- Lebensraeume
  - Nutzung durch Arten und Pflanzen
- Wuchsformen
  - Nutzung durch Pflanzen
- Gefährdungskategorien
  - Nutzung durch Arten
- Weitere Stammdaten mit vergleichbarer Loeschschutzlogik koennen nach demselben Muster angeschlossen werden.

## UX-Skizze
- Neue Tabellen-Spalte: `Nutzung`
- Inhalt:
  - einfache Zahl oder Badge bei nur einer Quelle
  - aggregierte Gesamtzahl bei mehreren Quellen
- Bei mehrfacher Nutzung aus verschiedenen Quellen kann zusaetzlich ein kompaktes `title` oder Tooltip genutzt werden, z. B.:
  - `3 Arten, 7 Pflanzen`
- Ziel ist schnelle Orientierung in der Listenansicht, ohne zuerst in Detailseiten oder Fehlermeldungen zu laufen.

## Technische Skizze
- Betroffene Livewire-Manager laden Nutzungszahlen ueber `withCount(...)` oder gezielte Aggregation.
- Bei mehrfachen Beziehungsquellen wird eine Summenspalte im Component-ViewModel gebildet.
- Beispiele:
  - `Habitat::withCount(['species', 'plants'])`
  - `Tag::withCount(['species'])`
  - `DistributionArea::withCount(['species'])` oder angepasst an das tatsaechliche Relationsmodell
- Die bestehende Delete-Protection im Backend bleibt unveraendert und ist weiterhin die letzte Instanz.

## Nicht-Ziele
- Keine Aenderung an fachlichen Loeschregeln
- Kein automatisches Bereinigen von Referenzen
- Keine Detailanalyse pro Nutzungskontext ueber die Listenansicht hinaus

## Offene Entscheidungen
- Welche Stammdaten-Manager werden im ersten Schritt aufgenommen?
 - Es können direkt alle 4 betroffenen Bereiche angepasst werden
- Soll nur die Summe angezeigt werden oder bei Mehrfachnutzung auch die Aufschluesselung?
 - Wenn ein Bereich Arten & Pflanzen betrifft, soll eine Aufschlüsselung nach beiden Nutzungsbereichen dargestellt werden.
- Soll der Loeschen-Button bei Nutzung > 0 nur fehlschlagen oder bereits deaktiviert wirken?
 - Deaktivieren

## Acceptance
- Admin sieht in relevanten Stammdaten-Listen pro Eintrag vor dem Loeschversuch eine sichtbare Nutzungszahl.
- Die Anzeige entspricht der realen Anzahl bestehender Referenzen.
- Bereits vorhandene Loeschsperren bleiben aktiv.
- Das Verhalten ist ueber mehrere Stammdaten-Bereiche konsistent.
