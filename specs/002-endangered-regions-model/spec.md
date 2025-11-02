# Feature Specification: Endangered Species Regional Model Refactoring

**Feature ID**: 002-endangered-regions-model
**Status**: Specification
**Created**: 2025-11-02
**Priority**: High
**Epic**: Base Data Architecture

---

## 1. Problem Statement

The current endangered species modeling is incorrect. Expert feedback indicates that the data model should reflect a two-step process:

1. **First Step**: Select/record all regions where a species is known to naturally occur or live
2. **Second Step**: For each of these regions, assign an endangered/conservation rating

Currently, the system only tracks endangered regions without distinguishing between regions where a species exists and regions where it's endangered. This conflates habitat distribution with conservation status.

---

## 2. Feature Overview

Refactor the endangered species data model to properly separate:
- **Geographic Distribution**: Which regions a species actually inhabits
- **Conservation Status**: The endangered/threat rating within each region where it occurs

This allows experts to document that a species exists in multiple regions, with different conservation ratings in each region.

---

## 3. User Scenarios

### Scenario 1: Recording Species Distribution
**Actor**: Data Administrator
**Goal**: Document all regions where a butterfly species naturally occurs

**Flow**:
1. Open species editing interface
2. Find the "Geographic Distribution" section
3. Select all regions where the species is known to live (e.g., NRW, NRBU, Bayern, etc.)
4. Save the species record
5. The selected regions are now marked as areas where this species naturally occurs

**Success Criteria**:
- Can select multiple regions for a species
- Selected regions are persisted and displayed
- Can modify the list of regions for existing species

---

### Scenario 2: Assigning Conservation Ratings
**Actor**: Conservation Expert
**Goal**: Assign appropriate endangered/threat ratings for a species in each region where it occurs

**Flow**:
1. Open species editing interface
2. In the species record, view the list of regions where it occurs
3. For each region, assign a conservation rating:
   - "nicht gefährdet" (not endangered) - *default*
   - "gefährdet" (endangered)
4. Save the ratings
5. The conservation status for each region is persisted

**Success Criteria**:
- Each region where species occurs has exactly one conservation rating
- Can change ratings for existing region assignments
- Cannot assign rating to region where species doesn't occur
- Default rating "nicht gefährdet" is automatically set when region is added

---

### Scenario 3: Viewing Regional Distribution Map
**Actor**: Public Visitor
**Goal**: See which species are endangered in specific regions

**Flow**:
1. Visit the regional distribution map
2. View regions with color intensity based on number of endangered species
3. See species that occur naturally in each region (regardless of status)
4. Distinguish between species presence and endangerment

**Success Criteria**:
- Map displays regions with species distribution data
- Color coding reflects endangered species count (not total species)
- Users understand the difference between "occurs here" vs "endangered here"

---

### Scenario 4: Future Enhancement - Additional Ratings
**Actor**: System Administrator
**Goal**: Add more granular conservation ratings (e.g., IUCN categories)

**Flow**:
1. In future iterations, add additional rating options:
   - "Vulnerable" / "Vulnerable"
   - "Critically Endangered" / "Vom Aussterben bedroht"
   - "Extinct in the Wild" / "In der Natur ausgestorben"
   - etc.
2. Existing region assignments continue to work
3. New ratings available for selection when editing species

**Success Criteria**:
- New ratings can be added without breaking existing data
- Rating system is extensible
- Database allows for future expansion

---

## 4. Functional Requirements

### FR1: Region Selection for Species
- Species must have a many-to-many relationship with regions representing natural distribution
- Users can select multiple regions where a species naturally occurs
- Users can modify the list of regions for existing species
- At least one region must be selectable for a species

### FR2: Conservation Rating System
- For each region-species pairing, a conservation rating must be assignable
- Initial ratings: "nicht gefährdet", "gefährdet"
- Rating system must be extensible for future additions
- A species without selected regions cannot have ratings

### FR3: Data Integrity
- A conservation rating can only exist for a region where the species occurs
- Deleting a region-species link must cascade delete any ratings for that pairing
- Ratings are required for all region-species pairings (no null values)
- Default rating for newly added region-species pairings is "nicht gefährdet" (not endangered)
- Users can change the default rating to "gefährdet" or other ratings as needed after assignment

### FR4: User Interface
- Species admin form shows two separate sections:
  - Geographic Distribution (region selection)
  - Conservation Status (rating assignment per region)
- Region selection and rating assignment are visually distinct
- Clear indication of regions with assigned ratings

### FR5: Data Migration
- Existing endangered_region associations must be preserved or migrated appropriately
- Existing species records continue to function during transition
- A migration strategy that handles existing data without loss

### FR6: Future Extensibility
- Ratings can be added/modified without database restructuring
- New ratings automatically available in all species editing forms
- Rating labels support localization (German/English initially)

---

## 5. Success Criteria

### Functional Success
- Users can assign species to multiple regions independently from conservation status
- Conservation ratings are applied per region, not globally to species
- Data model correctly reflects: species exists in region X with status Y, region Z with status Z
- All existing species maintain their data during migration

### User Experience Success
- Species editors can manage distribution and conservation in under 3 clicks per region
- Visual separation between "where it lives" and "how endangered it is" is clear
- Form validation prevents invalid state (rating without region assignment)

### System Success
- Public map correctly displays species distribution and endangerment separately
- Species browser filters work correctly with new data model
- API queries return accurate region-conservation data pairs
- System supports adding new rating levels in future without breaking existing functionality

---

## 6. Assumptions

1. **Rating Values**: Ratings are predefined options (enums), not free-text fields
2. **Required Ratings**: All region-species pairings must have a rating assigned (no null values)
3. **Default Rating**: When a region is added to a species, the default rating is automatically set to "nicht gefährdet" (not endangered)
4. **User Override**: Users can immediately change the default rating to "gefährdet" or other values after assignment
5. **Existing Data**: Current endangered_region data will be migrated; no loss of records
6. **Localization**: "nicht gefährdet" and "gefährdet" are German labels; English equivalents "not endangered" and "endangered" will be added
7. **Scale**: Initial implementation supports 2 ratings; architecture assumes room for 5-10 future ratings
8. **Uniqueness**: A species can only occur in a region once (no duplicate region assignments per species)

---

## 7. Key Entities & Data Model Changes

### Current Model Problem
```
Species --[many-to-many]--> EndangeredRegion
(species occurrence = endangered status)
```

### Proposed Model
```
Species --[many-to-many with pivot]--> Region
                                       ↓
                            ConservationRating
                         (not-endangered, endangered, ...)
```

### New/Modified Tables
- **regions** (new)
  - id
  - code (NRW, NRBU, etc.)
  - name
  - description
  - timestamps

- **species_region** (new pivot table)
  - id
  - species_id (FK)
  - region_id (FK)
  - conservation_status (enum: "nicht_gefährdet", "gefährdet")
  - timestamps

- **endangered_regions** (existing, deprecated)
  - To be archived/removed after data migration

### Relationships
- Species → has many Regions (through species_region pivot)
- Region → has many Species (through species_region pivot)
- species_region pivot → has conservation_status enum field

---

## 8. Out of Scope

- Detailed historical tracking of rating changes (version control)
- Automatic rating suggestions based on IUCN databases
- Photo/documentation attachments per region-status pairing
- Integration with external conservation databases (future enhancement)
- Advanced reporting/analytics on rating distributions

---

## 9. Risk Analysis

### Risks
1. **Data Migration Complexity**: Existing endangered_region data mapping to new structure
   - Mitigation: Automated migration script with validation checks
2. **Breaking Change**: Public API/views depend on current data structure
   - Mitigation: Update all components during implementation; use query builder consistency
3. **User Confusion**: Users may not understand distinction between distribution and endangerment
   - Mitigation: Clear UI labeling and help documentation

---

## 10. Dependencies & Notes

### Dependencies
- Database migration framework available
- Ability to modify Livewire components
- Access to species editing views

### Notes
- Rating system designed for future IUCN category integration
- "nicht gefährdet" / "gefährdet" labels follow German terminology from existing system
- Current public features (species browser, map, discover) will require updates to work with new model

---

## 11. Success Metrics

| Metric | Target | Verification |
|--------|--------|--------------|
| Data integrity during migration | 100% of existing records preserved | Automated test comparing before/after counts |
| Rating assignment completeness | 100% of region-species pairs have rating | Database query for null ratings |
| UI clarity | 90% of test users understand distinction | User testing feedback |
| System performance | Page load time under 2 seconds | Performance monitoring during species editing |
| Future extensibility | Ability to add rating in < 1 hour | Attempt to add new rating option |

---

## 12. Acceptance Criteria

- [ ] Data model correctly separates distribution from conservation status
- [ ] Species can be linked to multiple regions
- [ ] Each region assignment has a conservation rating
- [ ] Existing data migrated without loss
- [ ] Admin UI allows independent management of regions and ratings
- [ ] Public pages updated to work with new model
- [ ] All species-region queries return correct data pairs
- [ ] New rating options can be added without code changes
- [ ] System documentation updated
- [ ] Team validated model matches expert requirements
