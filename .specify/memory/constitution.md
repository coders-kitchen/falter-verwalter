<!--
  SYNC IMPACT REPORT

  Version: 0.0.0 → 1.0.0 (MINOR: Initial constitution with 5 core principles + 2 governance sections)

  Modified Principles: None (initial version)

  Added Sections:
  - Core Principles (5): Benutzerfreundlichkeit, Datenintegrität, Wartbarkeit, Suchbarkeit, Dokumentation
  - Sicherheit & Datenschutz
  - Performance & Skalierung
  - Governance

  Removed Sections: None

  Templates Requiring Updates:
  - ✅ .specify/templates/plan-template.md: Constitution Check section aligns with principles
  - ✅ .specify/templates/spec-template.md: Requirements section aligns with principles
  - ✅ .specify/templates/tasks-template.md: Task organization reflects principle-driven task types

  Follow-up TODOs: None
-->

# Falter Verwalter Constitution

## Core Principles

### I. Benutzerfreundlichkeit

End-Benutzer und Admin-Benutzer müssen die Anwendung intuitiv verstehen und nutzen können. Jede Oberfläche MUSS logisch strukturiert sein und klare Navigation bieten. Komplexe Funktionen MÜSSEN durch Tooltips, Hilfetext oder Dokumentation unterstützt werden. Ziel: Minimale Lernkurve für neue Benutzer.

**Rationale**: Eine Schmetterlingssammlung hat diverse Nutzer (Hobbyisten, Fachleute, Admins). Schlechte UX führt zu unbenutzter Datenbank und Frust.

---

### II. Datenintegrität

Alle erfassten Schmetterlingsdaten MÜSSEN korrekt, konsistent und verifizierbar sein. Validierungen MÜSSEN auf Eingabeebene und Datenbankebene erfolgen. Historische Änderungen SOLLTEN nachvollziehbar sein (Audit Trail). Datenredundanzen MÜSSEN vermieden werden.

**Rationale**: Falsche oder widersprüchliche Daten untergraben den Wert der gesamten Ressource und verschwenden Zeit bei der Recherche.

---

### III. Wartbarkeit

Der Code MUSS den Best Practices des gewählten Frameworks folgen. Komplexität MUSS begründet und dokumentiert werden. Tests MÜSSEN vorhanden sein für kritische Funktionen. Refactoring und Code Review SIND Standardpraxis, nicht optional.

**Rationale**: Wartbarer Code reduziert technische Schulden, ermöglicht schnelle Anpassungen und vereinfacht das Onboarding neuer Entwickler.

---

### IV. Suchbarkeit & Filterung

End-Benutzer MÜSSEN Schmetterlinge effizient nach Kategorien, Eigenschaften und Freitextsuche finden können. Abfragen MÜSSEN performant sein. Filter SOLLTEN kombinierbar sein und aussagekräftige Ergebnisse liefern.

**Rationale**: Eine Wissensdatenbank ohne gute Suchfunktion ist ineffektiv. Benutzer verlassen sich darauf, schnell relevante Informationen zu finden.

---

### V. Dokumentation

Benutzer-dokumentation (Guides, FAQs) MUSS für alle Hauptfunktionen vorhanden sein. Technische Dokumentation (Architektur, API, Datenbankschema) MUSS für Entwickler verfügbar sein. Dokumentation MUSS mit Code-Änderungen aktualisiert werden.

**Rationale**: Ohne Dokumentation sinkt die Benutzbarkeit exponentiell und die Wartbarkeit wird gefährdet.

---

## Sicherheit & Datenschutz

- **Authentifizierung**: Admin-Zugriff MUSS geschützt sein (sichere Passwörter, optionale 2FA).
- **Autorisierung**: Benutzerrollen (Admin, Contributer, Viewer) MÜSSEN klar definiert sein. Nur Admins dürfen kritische Daten ändern.
- **DSGVO-Compliance**: Persönliche Daten MÜSSEN minimal erfasst und sicher gespeichert werden. Benutzern MUSS Zugriff auf ihre Daten gewährt werden.
- **Datenverschlüsselung**: Sensible Verbindungen MÜSSEN verschlüsselt sein (HTTPS in Produktion).
- **Audit Logging**: Änderungen kritischer Daten SOLLTEN geloggt werden (Wer, Was, Wann).

**Rationale**: Schmetterlingssammlungen können Standortdaten enthalten, die schützenswert sind. Vertrauen in die Plattform erfordert Sicherheit.

---

## Performance & Skalierung

- **Response Times**: Normale Abfragen MÜSSEN unter 500ms antworten. Komplexe Suchen SOLLTEN unter 2s sein.
- **Datenbankoptimierung**: Häufig verwendete Abfragen MÜSSEN indexiert sein. Lazy Loading SOLLTE für große Datenmengen verwendet werden.
- **Benutzeranzahl**: Die App MUSS mindestens 100 gleichzeitige Benutzer unterstützen ohne Degradation.
- **Caching**: Häufig abgerufene Daten (z.B. Schmetterlingsverzeichnis) SOLLTEN gecacht werden.
- **Monitoring**: Performance-Metriken SOLLTEN überwacht werden, um Engpässe früh zu erkennen.

**Rationale**: Eine langsame App führt zu schlechtem User Experience und gefährdet die Akzeptanz.

---

## Governance

### Amendment Procedure

1. **Proposal**: Jedes Kernteammitglied kann Änderungen vorschlagen (GitHub Issue oder direkter Kontakt mit Peter).
2. **Review**: Peter (Projektleiter) bewertet die Vorschlag gegen die bestehenden Prinzipien.
3. **Decision**: Peter entscheidet, ob die Änderung angenommen wird.
4. **Documentation**: Akzeptierte Änderungen werden in dieser Verfassung dokumentiert mit neuer Version.
5. **Migration**: Falls erforderlich, werden abhängige Prozesse (Spec, Plan, Tasks) aktualisiert.

### Versioning Policy

- **MAJOR**: Entfernung oder grundlegende Umdefinition eines Prinzips (z.B. "Sicherheit optional" → "Sicherheit MUSS").
- **MINOR**: Neues Prinzip hinzugefügt oder bestehendes Prinzip erweitert.
- **PATCH**: Klarstellungen, Wording-Änderungen, Korrekturen ohne semantische Änderung.

### Compliance Review

- **Jede Feature-Specification** MUSS einen "Constitution Check" enthalten, der bestätigt, dass alle angewendbaren Prinzipien berücksichtigt sind.
- **Code Reviews** SOLLTEN überprüfen, ob Wartbarkeit und Best Practices eingehalten werden.
- **User Testing** SOLLTE durchgeführt werden, um Benutzerfreundlichkeit zu validieren.

---

**Version**: 1.0.0 | **Ratifiziert**: 2025-11-02 | **Zuletzt geändert**: 2025-11-02
