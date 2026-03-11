# Feature: Karteninteraktion entkoppeln und Geometrie-Ladung optimieren

## Ausgangslage
Aktuell werden bei Interaktionen auf der Karte potenziell große Datenmengen erneut vom Server geladen (mehrere MB), obwohl für den Nutzer bei Klick oft nur eine visuelle Hervorhebung und wenige Metadaten nötig sind.

## Ziel
Die Karten-UX soll reaktiver werden, indem:
1. Klicks auf Gebiete primär clientseitig verarbeitet werden (Highlight/Selection),
2. große Geometrie-Daten nicht bei jeder Interaktion erneut übertragen werden,
3. Gebietsinformationen bei Bedarf in separaten, kleinen Requests geladen werden.

## Produktentscheidung (Stand)
- Interaktion auf Gebiets-Klick soll zunächst ein leichtes Event/State-Update sein, nicht ein vollständiger GeoJSON-Reload.
- Gebietsinformationen können in separaten Requests geladen werden.
- Geometry-Ladestrategie : detailreiche Geometrie pro Gebiet nur on-demand (z. B. per Code), falls nötig.

## Architektur

### Variante Geometry strikt pro Gebiet (Code)
- `GET /api/map/areas/{code}/geometry`
- `GET /api/map/areas/{code}/meta`
- Vorteile: sehr kleine Initiallast
- Nachteile: mehr Requests, mehr Latenz bei häufigen Klicks

Begründung: Die Gesamt-Karte setzt sich aus mehreren einzelnen Segmenten zusammen. Das Auftrenen in einzelne Requests ermöglich, das gezielte Serverseitige-Update und nachladen durch den Client. Zu dem wird so vermieden, dass der Server für initiale Anfragen ein mehrere MB großes JSON zusammenführen und versenden muss. 

## Technische Leitplanken
- Clientseitiges Highlighting im Leaflet-Layer, kein unnötiger Re-render kompletter Layer.
- Serverseitige Caches/ETag/Last-Modified für Geometry-Endpoints.
- Code-basierte Endpoints sind sinnvoll für On-Demand-Nachladen.
- Debounce/Abort für konkurrierende Requests bei schneller Interaktion.

## API-Skizze
- `GET /api/map/areas/{code}/meta` -> Name, Code, Status, Zusatzinfos
- `GET /api/map/areas/{code}/meta?species_id={}` -> Meta infos + Details zu einem Falter. Ist optional
- optional: `GET /api/map/areas/{code}/geometry` -> detailreiche GeoJSON

## API-Contract Vorschlaege

Die folgenden Contracts sind Vorschlaege fuer eine erste, stabile Umsetzung. Sie sollen die API-Skizze konkretisieren, ohne das Feature-Dokument unnoetig zu ueberladen.

### 1. Gebiets-Meta ohne Artbezug

Request:
- `GET /api/map/areas/{code}/meta`

Beispiel-Response:

```json
{
  "data": {
    "code": "niedersachsen-west",
    "name": "Niedersachsen West",
    "species_distribution_area_count": 42
  }
}
```

Bedeutung der Felder:
- `code`: stabiler externer Schluessel des Gebiets
- `name`: Anzeigename fuer Popup und Listenansicht
- `species_distribution_area_count`: Anzahl der Datensaetze in `species_distribution_areas` fuer dieses Gebiet

### 2. Gebiets-Meta mit Artbezug

Request:
- `GET /api/map/areas/{code}/meta?species_id=123`

Beispiel-Response:

```json
{
  "data": {
    "code": "niedersachsen-west",
    "name": "Niedersachsen West",
    "species": {
      "id": 123,
      "threat_status": {
        "code": "VU",
        "label": "Vorwarnliste",
        "color": "#f59e0b"
      }
    }
  }
}
```

Bedeutung der Felder:
- `species.id`: referenzierte Art
- `species.threat_status.code`: fachlicher Statuscode fuer die Art in diesem Gebiet
- `species.threat_status.label`: lesbare Bezeichnung fuer Popup oder Detailpanel
- `species.threat_status.color`: optionale Farbe fuer bestehende Visualisierung

Verhalten:
- Ohne `species_id` wird nur die allgemeine Gebiets-Meta geliefert.
- Mit `species_id` wird die Response um artbezogene Daten erweitert.
- Wenn fuer die Art in diesem Gebiet kein Eintrag existiert, wird `200 OK` mit `species: null` zurueckgegeben.

### 3. Gebiets-Geometrie

Request:
- `GET /api/map/areas/{code}/geometry`

Beispiel-Response:

```json
{
  "data": {
    "code": "niedersachsen-west",
    "geometry": {
      "type": "Polygon",
      "coordinates": [
        [
          [8.1, 52.4],
          [8.2, 52.5],
          [8.3, 52.4],
          [8.1, 52.4]
        ]
      ]
    }
  }
}
```

Bedeutung der Felder:
- `code`: Zuordnung der Geometrie zum Gebiet
- `geometry`: gueltige GeoJSON-Geometry vom Typ `Polygon` oder `MultiPolygon`

### 4. Fehlerfaelle

Beispiel-Responses:

```json
{
  "message": "Distribution area not found."
}
```

Statuscodes:
- `200 OK`: erfolgreicher Abruf
- `404 Not Found`: Gebiet anhand des `code` nicht gefunden
- `422 Unprocessable Entity`: ungueltige `species_id`
- `304 Not Modified`: Geometrie unveraendert, wenn ETag/Conditional Request verwendet wird

## Akzeptanzkriterien
- Klick auf Gebiet führt ohne spürbaren Lag zur Hervorhebung.
- Keine wiederholte Übertragung großer GeoJSON-Payloads pro Klick.
- Gebietsdaten (Meta) werden separat und klein übertragen.

## Umsetzungsvorschlag für den nächsten Schritt
1. Ist-Analyse der aktuellen Map-Requests und Payload-Größen.
2. Einführung eines kleinen Meta-Endpoints pro Gebiet (code).
3. Anpassung Frontend-Flow: Klick -> Highlight sofort, Meta async nachladen.

## Entscheidungen
- Der Client-Cache wird mit einem TTL ausgerüstet, zusätzlich werden ETags genutzt. ETags dienen der Cache-Validierung bei geänderten GeoJSON-Dateien.. Begründung, die GeoJSONs werden nur sehr selten geupdated.
- Meta-Daten sind zur Zeit
  - in der großen Übersichtskarte:
    - Gesamtzahl-Falter im Gebiet (später eventuell Split nach Gefährdungsstatus). Hiermit ist für eine bestimmte distribution area, die Anzahl der Datensätz in der Tabelle `species_distribution_areas` für ein spefizisches Gebiet gemeint
    - Name des Gebietes
    - Abruf via `GET /api/map/areas/{code}/meta`
  - in der Falter-spezifischen Übersichtskarte:
    - Gefährdungstatus (für spezifische Art)
    - Name des Gebietes
    - Abruf via `GET /api/map/areas/{code}/meta?species_id={}`
- Die GeoJSONs werden, wie bisher auch, als Dateien auf dem Server abgelegt und im Datensatz der `distribution_area` wird der `geojson_path` für die schnellere Auflösung gespeichert.
- Es soll weiterhin bei Klick in der Karte auf einem Gebiet die bestehenden Pop-Ups erscheinen.
- Es soll der **code** des Gebietes genutzt werden. Begründung: Code ist eine stabile Information im Kontrast zur ID, die sich ggf. durch einen Re-Import oder löschen und wiederanlegen ändern kann.
- Inkrementelles Vorgehen
  1. Entkoppeln von Klick & Reload
  2. Neue API Endpunkte
  3. Finaler Umbau
