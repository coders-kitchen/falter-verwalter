# Feature: Hierarchische Ebenen fuer Verbreitungsgebiete und Karten-Layer

## Goal
Verbreitungsgebiete sollen kuenftig so strukturiert werden, dass auf der Kartenansicht Hintergrund-Layer und darauf liegende Detail-Layer fachlich sauber gepflegt und getrennt dargestellt werden koennen.

## Ausgangslage
- `distribution_areas` sind aktuell fachlich flach modelliert.
- Jedes Verbreitungsgebiet kann eine eigene GeoJSON-Geometrie haben.
- Die Kartenansicht behandelt alle Verbreitungsgebiete derzeit als gleichrangig.
- Fuer die Darstellung waere es hilfreich, grobe Flaechen wie Bundeslaender im Hintergrund zu zeigen und detailliertere Naturräume oder Teilregionen darueberzulegen.

## Problem
- Eine rein flache Struktur reicht nicht aus, um Karten-Layer gezielt nach fachlicher Ebene zu rendern.
- Eine reine Parent-Child-Hierarchie ist ebenfalls nicht ideal, weil einzelne Detailgebiete mehrere Bundeslaender ueberspannen koennen.
- Dadurch entsteht ein Konflikt zwischen administrativer Zugehoerigkeit und kartografischer Darstellung.

## Beispiel
- NRW
- Bergisches Land
- Eifel / Siebengebirge
- Niederrheinische Bucht

Fachlich sollen die Detailgebiete nicht zwingend exklusive Kinder eines einzelnen Bundeslands sein muessen. Sie sollen vor allem als eigene Kartenebene gepflegt und angezeigt werden koennen.

## Varianten

### Variante A: Direkte Hierarchie ueber `parent_id`
- Jedes Verbreitungsgebiet hat optional ein Parent-Gebiet.
- Beispiel: `Bergisches Land -> NRW`

Vorteile:
- Einfaches Datenmodell
- Bekanntes Muster, aehnlich wie bei `habitats`

Nachteile:
- Ein Gebiet kann nur genau ein Parent haben
- Bundeslanduebergreifende Detailgebiete werden unsauber modelliert
- Karten-Layer und fachliche Hierarchie werden zu stark gekoppelt

### Variante B: Ebenenmodell mit Layer-Fokus
- Verbreitungsgebiete gehoeren einer fachlichen Ebene an
- Beispiele fuer Ebenen:
  - Bundesland
  - Naturraum
  - Teilregion
- Die Kartenansicht rendert Ebenen gezielt in definierter Reihenfolge

Vorteile:
- Passt besser zur Kartenlogik mit Hintergrund- und Detail-Layern
- Bundeslanduebergreifende Gebiete bleiben problemlos moeglich
- Fachliche Ebene und visuelle Darstellung sind explizit modelliert

Nachteile:
- Etwas mehr initiale Modellierungsarbeit
- Fuer echte Unterordnungen braucht es zusaetzliche Regeln oder optionale Beziehungen

## Empfehlung
Variante B soll bevorzugt werden.

Begruendung:
- Das eigentliche Ziel ist nicht primaer eine Baumstruktur, sondern eine stabile Kartenlogik mit Hintergrund- und Detail-Layern.
- Verbreitungsgebiete muessen teilweise quer zu administrativen Grenzen modelliert werden koennen.
- Ein Ebenenmodell ist fuer kuenftige Kartendarstellungen flexibler als eine harte Parent-Child-Struktur.

## Vorschlag Datenmodell

### Neue Tabelle `distribution_area_levels`
Moegliche Felder:
- `id`
- `name`
- `code`
- `sort_order`
- `map_role`
- `description`

Beispielwerte:
- `bundesland`, `background`
- `naturraum`, `detail`
- `teilregion`, `detail`

### Erweiterung `distribution_areas`
Zusaetzliche Felder:
- `distribution_area_level_id` als Pflichtfeld
- optional spaeter `parent_id`, falls echte Unterordnungen zusaetzlich gebraucht werden

Bewertung:
- `distribution_area_level_id` bildet die primäre fachliche Struktur
- `parent_id` waere hoechstens eine optionale Zusatzbeziehung, nicht das Kernmodell

## Kartenlogik
- Hintergrund-Layer sollen aus groben Ebenen gespeist werden, z. B. Bundeslaender.
- Detail-Layer sollen aus feineren Ebenen gespeist werden, z. B. Naturräume.
- Nicht fuer jedes Bundesland muessen vollstaendige Unterregionen vorhanden sein.
- Die Karte darf auch funktionieren, wenn nur fuer einzelne Ebenen Geometrien gepflegt sind.

## Admin-Pflege
- Beim Anlegen oder Bearbeiten eines Verbreitungsgebiets muss eine Ebene ausgewaehlt werden.
- Die Listenansicht der Verbreitungsgebiete sollte die Ebene sichtbar machen.
- Optional kann spaeter nach Ebene gefiltert werden.
- Falls spaeter `parent_id` eingefuehrt wird, sollte dies nur fuer echte Unterordnung genutzt werden, nicht als Ersatz fuer Ebenen.

## Fachliche Leitplanken
- Eine Ebene beschreibt die Art der Flaeche, nicht zwingend ihre administrative Zugehoerigkeit.
- Mehrere Gebiete derselben Ebene duerfen sich ueberschneiden, wenn dies fachlich sinnvoll ist.
- Die Layer-Reihenfolge fuer die Karte soll nicht aus Namen abgeleitet werden, sondern explizit ueber die Ebene steuerbar sein.

## Moegliche Erweiterungen
- Eigene Stildefinition pro Ebene, z. B. Transparenz, Linienstaerke oder Default-Farbe
- Sichtbarkeitssteuerung pro Ebene in der Karten-UI
- Aggregation oder Filterung nach Ebene in oeffentlichen Ansichten
- Optionale Zuordnung eines Gebiets zu mehreren administrativen Referenzraeumen, falls spaeter noetig

## Offene Entscheidungen
- Welche Ebenen sollen im ersten Schritt offiziell unterstuetzt werden?
- Reicht zunaechst `background` und `detail` als `map_role`, oder soll direkt feiner unterschieden werden?
- Soll `distribution_area_level_id` sofort verpflichtend sein oder per Migration mit Default-Initialwert eingefuehrt werden?
- Wird zunaechst bewusst auf `parent_id` verzichtet, um das Modell schlank zu halten?

## Empfohlener erster Umsetzungsschritt
1. Ebenenmodell im Datenmodell einfuehren
2. Bestehende Verbreitungsgebiete einer Default-Ebene zuordnen
3. Admin-Maske um Ebenenauswahl erweitern
4. Kartenansicht nach Ebenen gruppiert ausliefern und rendern

## Acceptance
- Verbreitungsgebiete koennen einer fachlichen Ebene zugeordnet werden.
- Die Kartenansicht kann mindestens zwei Layer-Gruppen unterscheiden: Hintergrund und Detail.
- Bundeslanduebergreifende Detailgebiete lassen sich ohne fachlich falsche Parent-Struktur abbilden.
- Die Pflege bleibt auch dann moeglich, wenn nicht fuer jede Hintergrundflaeche vollstaendige Detailgebiete existieren.
