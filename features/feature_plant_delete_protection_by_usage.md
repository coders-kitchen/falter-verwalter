# Feature: Loesch-Schutz fuer Pflanzen bei Falter-Nutzung

## Goal
Admins sollen Pflanzen nicht versehentlich loeschen koennen, wenn diese bereits aktiv von Falterarten referenziert werden.

## Ausgangslage
- In der Pflanzenverwaltung koennen Pflanzen aktuell direkt geloescht werden.
- Pflanzen sind jedoch keine isolierten Inhalte, sondern werden fachlich in Falter-Pflanzen-Beziehungen verwendet.
- Dadurch kann das Loeschen einer Pflanze bestehende Zuordnungen und oeffentliche Auswertungen beschaedigen.

## Problem
- Vor dem Loeschversuch ist in der Pflanzenliste nicht sichtbar, ob eine Pflanze bereits von Arten genutzt wird.
- Die Loeschaktion wirkt aktuell verfuegbar, obwohl sie fachlich riskant ist.
- Admins laufen dadurch in vermeidbare Datenverluste oder muessen die Nutzung erst manuell an anderer Stelle pruefen.

## Vorschlag
- In der Pflanzen-Tabelle wird eine neue Spalte `Verwendung` oder `Nutzung` angezeigt.
- Die Spalte zeigt, wie oft die Pflanze aktuell in `species_plant` referenziert wird.
- Pflanzen ohne Falterbezug zeigen `0`.
- Pflanzen mit Nutzung > `0` zeigen einen sichtbaren Badge.
- Der Loeschen-Button wird bei Nutzung > `0` deaktiviert.
- Die serverseitige `delete()`-Logik blockiert das Loeschen ebenfalls weiterhin verbindlich.

## Fachlicher Umfang
- Direkte Pflanzennutzung durch Falterarten ueber `species_plant`
- Relevante Nutzungsarten:
  - Nektarpflanze
  - Futterpflanze
- Primaer/Sekundaer-Praeferenzen muessen fuer den Loesch-Schutz nicht getrennt gezaehlt werden; entscheidend ist, dass eine fachliche Referenz existiert.

## UX-Skizze
- Neue Tabellen-Spalte: `Nutzung`
- Inhalt:
  - Gesamtzahl aller referenzierenden Falter-Beziehungen
- Optionaler Tooltip:
  - z. B. `2 Nektar, 3 Futter`
- Loeschen:
  - deaktiviert, wenn Nutzung > `0`
  - Tooltip oder `title` erklaert kurz, warum

## Technische Skizze
- `PlantManager` laedt Nutzungszahlen ueber passende Relationen bzw. `withCount(...)`.
- Falls nur die reine Referenzzahl relevant ist:
  - Count auf `speciesAsHostPlant`
- Falls eine Aufschluesselung nach Nutzungstyp gewuenscht ist:
  - getrennte Counts fuer Nektar- und Futterbeziehungen auf Basis der Pivot-Flags
- `delete(Plant $plant)` prueft vor dem Loeschen, ob Eintraege in `species_plant` existieren.
- Bei vorhandener Nutzung:
  - kein Delete
  - Admin-Notification mit kurzer Fehlermeldung

## Abgrenzung
- Keine Aenderung an Gattungszuordnungen (`species_genus`), da diese nicht die konkrete Pflanze selbst referenzieren.
- Kein automatisches Entfernen bestehender Falter-Pflanzen-Beziehungen.
- Keine Aenderung an der oeffentlichen Darstellung; es geht primaer um Admin-Schutz und Datenintegritaet.

## Offene Entscheidungen
- Soll nur die Gesamtzahl gezeigt werden oder direkt die Aufschluesselung nach Nektar/Futter?
 - Arten-Gesamtzahl reicht.
- Soll die Nutzungszahl alle Referenzen zaehlen oder eindeutig referenzierende Arten?
 - Alle Referenzen
- Soll die Pflanzenliste spaeter analog zu den Stammdaten allgemein um weitere Schutzsignale ergaenzt werden?
 - Für den Start nur das minimal Feature "nicht löschen bei Nutzung"

## Acceptance
- Admin sieht in der Pflanzenliste vor dem Loeschversuch, ob eine Pflanze von Faltern genutzt wird.
- Pflanzen mit Falter-Referenzen koennen nicht geloescht werden.
- Der Loeschen-Button ist bei aktiver Nutzung bereits in der UI deaktiviert.
- Die serverseitige Loeschsperre verhindert Umgehungen der UI.
