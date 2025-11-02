# Feature Specification: Admin-Bereich für Basis-Daten-Verwaltung

**Feature Branch**: `001-admin-basis-daten`
**Created**: 2025-11-02
**Status**: Draft
**Input**: User description: "Admin-Bereich zu Anlage neuer Basis-Daten für die Verwaltung von Schmetterlingsdaten"

## User Scenarios & Testing *(mandatory)*

<!--
  IMPORTANT: User stories should be PRIORITIZED as user journeys ordered by importance.
  Each user story/journey must be INDEPENDENTLY TESTABLE - meaning if you implement just ONE of them,
  you should still have a viable MVP (Minimum Viable Product) that delivers value.
  
  Assign priorities (P1, P2, P3, etc.) to each story, where P1 is the most critical.
  Think of each story as a standalone slice of functionality that can be:
  - Developed independently
  - Tested independently
  - Deployed independently
  - Demonstrated to users independently
-->

### User Story 1 - Admin erstellt neue Schmetterlingsart (Priority: P1)

Ein Admin benötigt die Möglichkeit, neue Schmetterlingsarten mit umfassenden Informationen in die Datenbank einzupflegen. Dies ist die Kernfunktionalität, da ohne neue Daten keine App-Funktionalität möglich ist. Der Admin sollte ein strukturiertes Formular ausfüllen können mit Basis-Informationen, Verbreitung, Ökologie, morphologischen Daten, Lebenszyklusdaten und Schutzstatus. Besonders wichtig: Bei den Lebenszyklusdaten kann eine Art mehrere Generationen pro Jahr ausbilden, die jeweils unterschiedliche Flugzeiten haben.

**Why this priority**: Dies ist die MVP-kritische Funktion. Ohne die Möglichkeit, Schmetterlingsarten mit vollständigen Informationen zu erfassen, kann die gesamte App nicht funktionieren. Dies ist das Fundament aller anderen Features.

**Independent Test**: Diese User Story kann vollständig getestet werden, indem ein Admin über das Admin-Panel eine neue Schmetterlingsart mit allen Attributen erstellt und überprüft wird, ob diese in der Datenbank persistent gespeichert und später vollständig abrufbar ist. Besonders wird getestet, dass Arten mit mehreren Generationen korrekt erfasst und angezeigt werden.

**Acceptance Scenarios**:

1. **Given** Admin ist angemeldet und im Admin-Bereich, **When** Admin klickt auf "Neue Art erstellen" und füllt das Formular mit gültigen Daten aus (Name, Wissenschaftlicher Name, Familie, Verbreitungsgebiete, Habitate, Raupenpflanzen, Größenkategorie, Färbung, Generationen/Jahr, Flugzeiten pro Generation, Überwinterungsstadium, Schutzstatus), **Then** wird die neue Art in der Datenbank gespeichert und eine Bestätigungsmeldung wird angezeigt

2. **Given** Admin erfasst eine Schmetterlingsart mit 2 Generationen pro Jahr (z.B. Gen. 1: April-Mai, Gen. 2: Juli-August), **When** Admin speichert die Art, **Then** werden beide Generationen mit ihren jeweiligen Flugzeiten korrekt gespeichert und können später abgerufen werden

3. **Given** Admin hat eine neue Art erstellt, **When** Admin öffnet die Artenübersicht, **Then** ist die neue Art in der Liste sichtbar mit grundlegenden Informationen

4. **Given** Admin füllt das Formular unvollständig aus (z.B. fehlt der Name), **When** Admin klickt "Speichern", **Then** wird eine Validierungsmeldung angezeigt und die Art wird nicht gespeichert

---

### User Story 2 - Admin verwaltet Familien von Schmetterlingen (Priority: P2)

Ein Admin muss Schmetterlingsarten Familie zuordnen können. Um die Datenintegrität sicherzustellen, sollten Familien als separate Basis-Entität verwaltet werden können. Der Admin sollte neue Familien anlegen, bearbeiten und löschen können.

**Why this priority**: Notwendig für korrekte Datenorganisation und die Voraussetzung für Story 1 (Art-Erfassung mit Familien-Zuordnung). P2 weil die Basis-Daten-Struktur vorhanden sein muss, bevor viele Arten erfasst werden.

**Independent Test**: Diese User Story kann getestet werden, indem ein Admin neue Familien anlegt und diese dann bei der Art-Erfassung zur Auswahl stehen.

**Acceptance Scenarios**:

1. **Given** Admin ist im Admin-Bereich, **When** Admin navigiert zu "Familien verwalten", **Then** wird eine Liste aller existierenden Familien angezeigt
2. **Given** Admin ist in der Familien-Verwaltung, **When** Admin klickt "Neue Familie" und gibt einen Namen ein (z.B. "Nymphalidae"), **Then** wird die Familie gespeichert und in der Liste angezeigt
3. **Given** Eine Familie existiert in der Datenbank, **When** Admin versucht, die Familie zu löschen, **Then** wird überprüft, ob diese Familie noch von Arten verwendet wird - wenn ja, wird eine Warnung angezeigt

---

### User Story 3 - Admin verwaltet Habitate (Priority: P2)

Ein Admin muss Lebensräume als vordefinierte Basis-Daten mit hierarchischer Struktur verwalten können. Habitate bestehen aus Oberkategorien (z.B. "Wald", "Ruderalflächen") und zugehörigen Unterkategorien (z.B. "Laubwald", "Nadelwald", "Wegrand"). Der Admin sollte neue Habitate anlegen, bearbeiten und löschen können.

**Why this priority**: Notwendig für Datenqualität und konsistente Kategorisierung von Lebensräumen. Habitate werden sowohl für Schmetterlingsarten als auch für Pflanzen verwendet.

**Independent Test**: Diese User Story kann getestet werden, indem ein Admin neue Habitate mit Hierarchie anlegt und diese bei der Art- und Pflanzeneingabe zur Auswahl stehen.

**Acceptance Scenarios**:

1. **Given** Admin ist im Admin-Bereich, **When** Admin navigiert zu "Habitate verwalten", **Then** wird eine hierarchische Liste aller Habitate mit Oberkategorien und Unterkategorien angezeigt
2. **Given** Admin ist in der Habitat-Verwaltung, **When** Admin erstellt eine neue Oberkategorie (z.B. "Wald") und Unterkategorien (z.B. "Laubwald", "Nadelwald"), **Then** werden diese mit Hierarchie gespeichert
3. **Given** Ein Habitat existiert und wird von Arten oder Pflanzen verwendet, **When** Admin versucht, das Habitat zu löschen, **Then** wird eine Warnung angezeigt und die Löschung wird verhindert

---

### User Story 4 - Admin verwaltet Pflanzen (Priority: P2)

Ein Admin muss Futter- und Raupenpflanzen mit umfangreichen botanischen Merkmalen erfassen können, um Gärtnern und Interessierten bei der Gartengestaltung zu helfen. Pflanzen enthalten detaillierte Informationen wie Name, Wissenschaftlicher Name, ökologische Zeigerwerte (Lichtzahl, Temperaturzahl, etc.), Lebensart, Blützeit, Höhe, Standort, Einheimischstatus und Invasivitäts-Status. Diese Informationen ermöglichen es Gärtnern, gezielt Pflanzen auszuwählen, die:
- zum eigenen Garten passen (Standort, Licht, Feuchtigkeit)
- Schmetterlinge anlocken (als Raupenfutter oder Nektarquelle)
- umweltgerecht sind (einheimisch, nicht invasiv)

Diese Pflanzen können später bei der Schmetterlingsart-Erfassung als Raupenfutter-Pflanzen zugeordnet werden.

**Why this priority**: Notwendig für die Erfassung ökologischer Daten und für die Art-Erfassung (User Story 1). Pflanzen sind Basis-Daten, die vor Nutzung durch Schmetterlingsarten vorhanden sein sollten. Die umfangreichen Merkmale sind essentiell für die Gartengestaltungs-Funktionalität der App.

**Independent Test**: Diese User Story kann getestet werden, indem ein Admin neue Pflanzen mit allen botanischen Merkmalen anlegt und diese dann bei der Art-Erfassung zur Auswahl stehen. Es wird überprüft, dass Benutzer später anhand der ökologischen Zeigerwerte Pflanzen filtern können.

**Acceptance Scenarios**:

1. **Given** Admin ist im Admin-Bereich, **When** Admin navigiert zu "Pflanzen verwalten", **Then** wird eine Liste aller erfassten Pflanzen mit grundlegenden Informationen angezeigt

2. **Given** Admin ist in der Pflanzen-Verwaltung, **When** Admin klickt "Neue Pflanze" und füllt umfangreiche Daten ein (Name, Wissenschaftlicher Name, Lichtzahl, Temperaturzahl, Kontinentalitätszahl, Reaktionszahl, Feuchtezahl, Lebensart, Blützeit, Höhe, Standort, Einheimisch, Invasiv, Überdauerungsorgane), **Then** wird die Pflanze mit allen Attributen gespeichert und ist als Raupenpflanze für Schmetterlingsarten verfügbar

3. **Given** Eine Pflanze existiert, **When** Admin bearbeitet ökologische Zeigerwerte (z.B. Lichtzahl), **Then** werden die Änderungen persistent gespeichert und sind unmittelbar verfügbar

4. **Given** Eine invasive und nicht-einheimische Pflanze wird erfasst, **When** Admin speichert diese Pflanze, **Then** werden die Invasiv/Einheimisch-Flags korrekt gespeichert und können für Filterung genutzt werden

---

### User Story 5 - Admin verwaltet Lebensarten von Pflanzen (Priority: P2)

Ein Admin muss Lebensarten (Lebensformen) von Pflanzen als Basis-Katalog verwalten können. Lebensarten wie "Baum", "Strauch", "Kraut", "Gras" etc. sind essentiell zur Klassifizierung von Pflanzen. Der Admin sollte neue Lebensarten anlegen, bearbeiten und löschen können.

**Why this priority**: Notwendig für die strukturierte Erfassung von Pflanzendaten (User Story 4). Lebensarten sind Basis-Daten, die vor Nutzung durch Pflanzen vorhanden sein sollten. P2 weil die Basis-Klassifizierung vorhanden sein muss, bevor viele Pflanzen erfasst werden.

**Independent Test**: Diese User Story kann getestet werden, indem ein Admin neue Lebensarten anlegt und diese dann bei der Pflanzeneingabe zur Auswahl stehen.

**Acceptance Scenarios**:

1. **Given** Admin ist im Admin-Bereich, **When** Admin navigiert zu "Lebensarten verwalten", **Then** wird eine Liste aller verfügbaren Lebensarten angezeigt (z.B. Baum, Strauch, Kraut, Gras, Farn)

2. **Given** Admin ist in der Lebensarten-Verwaltung, **When** Admin klickt "Neue Lebensart" und gibt einen Namen ein (z.B. "Kletterstrauch") und optionale Beschreibung, **Then** wird die Lebensart gespeichert und in der Liste angezeigt

3. **Given** Eine Lebensart existiert und wird von Pflanzen verwendet, **When** Admin versucht, die Lebensart zu löschen, **Then** wird überprüft, ob diese Lebensart noch von Pflanzen verwendet wird - wenn ja, wird eine Warnung angezeigt

---

### User Story 6 - Admin verwaltet Verbreitungsgebiete (Priority: P2)

Ein Admin muss Verbreitungsgebiete (Regionen, Kontinente, Länder) als vordefinierte Basis-Daten verwalten können, um eine konsistente Kategorisierung zu ermöglichen. Der Admin sollte neue Gebiete anlegen, bearbeiten und löschen können.

**Why this priority**: Notwendig für Datenqualität und Konsistenz. Erlaubt Admins, ein vordefiniertes Verzeichnis zu pflegen, statt dass Verbreitungsinformationen freitext eingegeben werden.

**Independent Test**: Diese User Story kann getestet werden, indem ein Admin neue Verbreitungsgebiete anlegt und bei der Art-Erfassung auswählt.

**Acceptance Scenarios**:

1. **Given** Admin ist im Admin-Bereich, **When** Admin navigiert zu "Verbreitungsgebiete", **Then** wird eine Liste aller definierten Gebiete angezeigt
2. **Given** Admin ist in der Verbreitungsgebiete-Verwaltung, **When** Admin klickt "Neues Gebiet" und gibt ein Gebiet ein (z.B. "Mitteleuropa"), **Then** wird das Gebiet gespeichert
3. **Given** Ein Verbreitungsgebiet existiert, **When** Admin bearbeitet den Namen des Gebiets, **Then** werden alle Schmetterlingsarten, die dieses Gebiet verwenden, korrekt aktualisiert

### Edge Cases

- Was passiert, wenn ein Admin versucht, ein Basis-Datum mit einem Namen zu erstellen, der bereits existiert (Duplikat)? → System sollte Warnung anzeigen oder Duplikat verhindern
- Wie wird mit fehlerhaft eingegebenen Daten umgegangen, wenn ein Admin ein Formular absendet? → Validierungsmeldungen sollten angezeigt werden
- Was passiert, wenn ein Admin versucht, ein Basis-Datum zu löschen, das von anderen Daten abhängig ist? → System sollte warnen oder Löschung verhindern
- Wie wird die Benutzerfreundlichkeit bei vielen Basis-Daten-Einträgen gewährleistet? → Paginierung, Suchfunktion oder Filterung sollte implementiert sein
- Wie werden mehrere Generationen einer Art erfasst und verwaltet? → Admins sollten einfach neue Generationen hinzufügen/entfernen können mit jeweiligen Flugzeiten
- Was passiert, wenn Admin einen ungültigen Monatsbereich für eine Generation eingibt (z.B. negative Werte, Monat > 12)? → Validierung sollte Fehlermeldung zeigen

## Requirements *(mandatory)*

<!--
  ACTION REQUIRED: The content in this section represents placeholders.
  Fill them out with the right functional requirements.
-->

### Functional Requirements

- **FR-001**: System MUSS Admin-Benutzer authentifizieren, bevor Zugriff auf Admin-Funktionen gewährt wird
- **FR-002**: System MUSS Admins ermöglichen, neue Schmetterlingsarten mit folgenden Attributen zu erstellen:
  - Basis: Name, Wissenschaftlicher Name, Familie
  - Verbreitung: Verbreitungsgebiete (ein oder mehrere), Habitate (ein oder mehrere)
  - Ökologie: Raupenpflanzen/Futterpflanzen (ein oder mehrere)
  - Morphologie: Größenkategorie (XS/S/M/L/XL), Färbung/Grundfärbung, Besondere Merkmale, Geschlechtsunterschiede (optional)
  - Lebenszyklusdaten: Anzahl Generationen/Jahr (mit Möglichkeit mehrere Generationen zu erfassen), Flugzeit pro Generation (Monate), Raupenentwicklungsdauer (optional), Überwinterungsstadium
  - Status: Rote-Liste-Status, Häufigkeit/Trend, Schutzstatus

- **FR-003**: System MUSS alle erfassten Schmetterlingsarten persistent in einer Datenbank speichern, einschließlich aller morphologischen, ökologischen und Lebenszyklusdaten
- **FR-004**: System MUSS Admins ermöglichen, neue Familien zu erstellen und zu verwalten
- **FR-005**: System MUSS Admins ermöglichen, Verbreitungsgebiete zu erstellen und zu verwalten
- **FR-005a**: System MUSS Admins ermöglichen, Habitate mit hierarchischer Struktur (Oberkategorie/Unterkategorie) zu erstellen und zu verwalten
- **FR-005b**: System MUSS Admins ermöglichen, Pflanzen (Futter- und Raupenpflanzen) mit umfangreichen botanischen Merkmalen zu erfassen:
  - Basis: Name, Wissenschaftlicher Name, Familie/Gattung
  - Ökologische Zeigerwerte: Lichtzahl, Temperaturzahl, Kontinentalitätszahl, Reaktionszahl (pH), Feuchtezahl, Feuchtewechsel, Stickstoffzahl
  - Morphologie: Lebensart, Höhe, Lebensdauer
  - Phänologie: Blützeit, Blütenfarbe (optional)
  - Biogeografie: Habitate, Standort, Einheimisch, Invasiv, Gefährdung
  - Überdauerungsorgane: Typ
- **FR-006**: System MUSS Datenvalidierung durchführen (Pflichtfelder, eindeutige Werte) und Validierungsfehler benutzerfreundlich anzeigen
- **FR-007**: System MUSS Schmetterlingsarten bearbeiten und aktualisieren ermöglichen
- **FR-008**: System MUSS eine Übersicht aller erfassten Schmetterlingsarten für Admins bereitstellen (mit Paginierung bei großen Datenmengen)
- **FR-009**: System MUSS Vorsichtsmaßnahmen implementieren, um zu verhindern, dass Basis-Daten gelöscht werden, die von anderen Einträgen abhängig sind (Referenzialintegrität)
- **FR-010**: System MUSS Benutzerfreundlichkeit bieten durch klare Navigation und intuitive Formulare im Admin-Bereich
- **FR-011**: System MUSS die Erfassung mehrerer Generationen pro Schmetterlingsart unterstützen, bei denen jede Generation unterschiedliche Flugzeiten (Monate) haben kann
- **FR-012**: System MUSS Morphologische Daten korrekt speichern und in einer durchsuchbaren Form präsentieren (Größenkategorie, Färbung, Merkmale)
- **FR-013**: System MUSS Admins ermöglichen, Lebensarten (Pflanzen-Lebensformen) zu erstellen und zu verwalten
- **FR-014**: System MUSS ökologische Zeigerwerte (1-9 Skalen) für Pflanzen speichern und validieren: Lichtzahl, Temperaturzahl, Kontinentalitätszahl, Reaktionszahl, Feuchtezahl, Stickstoffzahl
- **FR-015**: System MUSS Gärtner-relevante Pflanzendaten unterstützen und durchsuchbar machen: Lebensart, Höhe, Blützeit, Standort, Einheimisch/Invasiv-Status

### Key Entities

- **Schmetterlingsart (Species)**: Repräsentiert eine einzelne Schmetterlingsart mit umfangreichen Attributen:
  - **Basis-Informationen**: Name, Wissenschaftlicher Name, Familie (Fremdschlüssel)
  - **Verbreitung & Habitat**: Verbreitungsgebiete (many-to-many), Habitate (many-to-many)
  - **Ökologie**: Raupenpflanzen/Futterpflanzen (many-to-many)
  - **Morphologische Daten**: Größenkategorie (XS/S/M/L/XL), Färbung/Grundfärbung (Text), Besondere Merkmale (Text), Geschlechtsunterschiede (Text, optional)
  - **Lebenszyklusdaten**: Anzahl der Generationen pro Jahr (1-N), Flugzeit der einzelnen Generationen (Monate), Raupenentwicklungsdauer (Tage, optional), Überwinterungsstadium (Ei/Raupe/Puppe/Imago)
  - **Status & Schutz**: Rote-Liste-Status (Deutschland, EU, optional), Häufigkeit/Trend, Schutzstatus (keine/national/europäisch)

- **Familie (Family)**: Taxonomische Familie von Schmetterlingen (z.B. Nymphalidae, Pieridae). Attribute: Name, Beschreibung (optional)

- **Verbreitungsgebiet (Distribution Area)**: Geografisches Gebiet oder Region, in dem Schmetterlinge vorkommen (z.B. Mitteleuropa, Südostasien). Attribute: Name, Beschreibung (optional)

- **Habitat**: Repräsentiert Lebensräume mit hierarchischer Struktur (Oberkategorie und Unterkategorie). Beispiele: Wald → Laubwald, Wald → Nadelwald, Ruderalflächen → Wegrand, Ruderalflächen → Schuttplatz. Attribute: Name, Oberkategorie (optional, für Hierarchie), Beschreibung (optional). Wird verwendet für Schmetterlingsarten und Pflanzen

- **Pflanze (Plant)**: Repräsentiert Futter- und Raupenpflanzen für Schmetterlinge mit detaillierten botanischen Merkmalen zur Unterstützung der Gartengestaltung. Attribute:
  - **Basis**: Name, Wissenschaftlicher Name, Familie/Gattung (optional)
  - **Ökologie**: Habitate (many-to-many), Lichtzahl (1-9), Temperaturzahl (1-9), Kontinentalitätszahl (1-9), Reaktionszahl (pH, 1-9), Feuchtezahl (1-9), Feuchtewechsel (Text/Kategorie), Stickstoffzahl (1-9)
  - **Morphologie & Lebensraum**: Lebensart (Fremdschlüssel zu Lebensart-Entity), Höhe (cm, optional), Lebensdauer (einjährig/zweijährig/mehrjährig)
  - **Phänologie**: Blützeit (Monate), Blütenfarbe (optional)
  - **Biogeografie & Schutz**: Standort (Text/Kategorie), Einheimisch (ja/nein), Invasiv (ja/nein), Gefährdung/Bedrohung (Text, optional)
  - **Überdauerungsorgane**: Typ (Samen, Knolle, Zwiebel, Rhizom, etc., optional)

- **Lebensart (Life Form)**: Repräsentiert die biologische Lebensform von Pflanzen. Diese Entity ermöglicht eine strukturierte Verwaltung und wird bei der Pflanzeneingabe als Auswahl angeboten. Attribute: Name (z.B. "Baum", "Strauch", "Kraut", "Gras"), Beschreibung (optional), Beispiele (optional)

- **Größenkategorie (Size Category)**: Vordefinierte Größenkategorien für einfache Nutzung durch Laien und Admins (keine separate Verwaltung nötig, sondern als feste Basis-Enumerationen):
  - **XS** (sehr klein): < 2,5 cm Flügelspannweite
  - **S** (klein): 2,5 - 3,5 cm
  - **M** (mittelgroß): 3,5 - 5 cm
  - **L** (groß): 5 - 6,5 cm
  - **XL** (sehr groß): ≥ 6,5 cm

## Success Criteria *(mandatory)*

<!--
  ACTION REQUIRED: Define measurable success criteria.
  These must be technology-agnostic and measurable.
-->

### Measurable Outcomes

- **SC-001**: Admins können eine neue Schmetterlingsart in weniger als 2 Minuten erfassen und speichern
- **SC-002**: Mindestens 95% der erfassten Daten werden persistent und korrekt in der Datenbank gespeichert
- **SC-003**: Formularvalidierung zeigt Fehler innerhalb von unter 500ms an
- **SC-004**: Admin kann eine Familie oder ein Verbreitungsgebiet in unter 1 Minute erstellen
- **SC-005**: Die Admin-Oberfläche ermöglicht die Verwaltung von mindestens 1000 Schmetterlingsarten ohne spürbare Leistungsverschlechterung
- **SC-006**: 90% der Admins können die Grundfunktionen (Art erstellen, Familie verwalten, Gebiet verwalten) beim ersten Versuch ohne Anleitung durchführen
- **SC-007**: Kein Datenverlust bei der Erstellung, Bearbeitung oder Löschung von Basis-Daten (Audit Trail / Protokollierung)

## Constitution Check

Folgende Prinzipien aus der Verfassung "Falter Verwalter" werden in dieser Feature berücksichtigt:

- ✅ **Benutzerfreundlichkeit**: Admin-Formulare werden intuitiv gestaltet mit klarer Navigation
- ✅ **Datenintegrität**: Validierung und Referenzialintegrität-Prüfung sind implementiert
- ✅ **Wartbarkeit**: Saubere Trennung von Admin-UI und Datenmodell
- ✅ **Sicherheit & Datenschutz**: Admin-Authentifizierung und Autorisierung erforderlich

## Assumptions

- Es existiert bereits ein Authentifizierungssystem, das Admin-Benutzer identifiziert
- Die Datenbankstruktur ist vorhanden oder wird parallel implementiert
- Admins sind vertraut mit Standard-CRUD-Operationen und Formularen
- Basis-Daten wie Familien und Verbreitungsgebiete sind begrenzt (< 10.000 Einträge pro Kategorie)
- Duplikat-Prävention wird durch Datenbank-Constraints oder Geschäftslogik implementiert
