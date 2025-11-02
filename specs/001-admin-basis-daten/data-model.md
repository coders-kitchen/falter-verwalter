# Data Model: Admin-Bereich für Basis-Daten-Verwaltung

**Date**: 2025-11-02
**Phase**: 1 - Design & Contracts
**Framework**: Laravel 11 + Eloquent ORM + MySQL 8+

---

## Entity Relationship Diagram

```
User (Admin)
├─ 1:N → Species (erstellt/bearbeitet)
├─ 1:N → Family
├─ 1:N → Habitat
├─ 1:N → Plant
├─ 1:N → LifeForm
└─ 1:N → DistributionArea

Family (Familie)
└─ 1:N → Species

Species (Schmetterlingsart)
├─ FK: family_id → Family
├─ M2M: distribution_areas → DistributionArea (pivot: species_distribution)
├─ M2M: habitats → Habitat (pivot: species_habitat)
└─ M2M: host_plants → Plant (pivot: species_plant)

Habitat (Lebensraum - hierarchisch)
├─ Self: parent_id (für Hierarchie Wald → Laubwald)
├─ M2M: species → Species
└─ M2M: plants → Plant

Plant (Pflanze/Raupenfutterpflanze)
├─ FK: life_form_id → LifeForm
├─ M2M: habitats → Habitat
└─ M2M: host_for_species → Species

LifeForm (Lebensart)
└─ 1:N → Plant

DistributionArea (Verbreitungsgebiet)
└─ M2M: species → Species
```

---

## Entity Specifications

### 1. User (Admin-Benutzer)

**Table**: `users`

**Columns**:
| Column | Type | Nullable | Unique | Index | Notes |
|--------|------|----------|--------|-------|-------|
| id | BIGINT | N | Y | PK | Auto-increment |
| name | VARCHAR(255) | N | N | N | Admin Name |
| email | VARCHAR(255) | N | Y | Y | Email (unique) |
| password | VARCHAR(255) | N | N | N | Hashed Password |
| role | ENUM | N | N | Y | admin/viewer |
| is_active | BOOLEAN | N | N | Y | Default: true |
| last_login_at | TIMESTAMP | Y | N | N | Audit Trail |
| created_at | TIMESTAMP | N | N | N | Larvel timestamp |
| updated_at | TIMESTAMP | N | N | N | Laravel timestamp |

**Eloquent Model**:
```php
class User extends Model
{
    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    public function species() { return $this->hasMany(Species::class); }
    public function families() { return $this->hasMany(Family::class); }
    public function habitats() { return $this->hasMany(Habitat::class); }
    public function plants() { return $this->hasMany(Plant::class); }
    public function lifeforms() { return $this->hasMany(LifeForm::class); }
    public function distributions() { return $this->hasMany(DistributionArea::class); }
}
```

**Validierung** (Form Request):
- name: required|string|max:255
- email: required|email|unique:users
- password: required|string|min:8|confirmed
- role: required|in:admin,viewer

---

### 2. Family (Schmetterlingsarten-Familie)

**Table**: `families`

**Columns**:
| Column | Type | Nullable | Unique | Index | Notes |
|--------|------|----------|--------|-------|-------|
| id | BIGINT | N | Y | PK | Auto-increment |
| user_id | BIGINT | N | N | Y | FK → users |
| name | VARCHAR(255) | N | Y | Y | z.B. "Nymphalidae" |
| description | TEXT | Y | N | N | Optional |
| created_at | TIMESTAMP | N | N | N | Laravel timestamp |
| updated_at | TIMESTAMP | N | N | N | Laravel timestamp |

**Eloquent Model**:
```php
class Family extends Model
{
    protected $fillable = ['name', 'description'];

    public function user() { return $this->belongsTo(User::class); }
    public function species() { return $this->hasMany(Species::class); }
}
```

**Validierung**:
- name: required|string|max:255|unique:families
- description: nullable|string|max:1000

---

### 3. Species (Schmetterlingsart)

**Table**: `species`

**Columns**:
| Column | Type | Nullable | Unique | Index | Notes |
|--------|------|----------|--------|-------|-------|
| id | BIGINT | N | Y | PK | Auto-increment |
| user_id | BIGINT | N | N | Y | FK → users |
| family_id | BIGINT | N | N | Y | FK → families |
| name | VARCHAR(255) | N | Y | Y | Deutscher Name |
| scientific_name | VARCHAR(255) | N | Y | Y | Lateinischer Name |
| size_category | ENUM | N | N | Y | XS/S/M/L/XL |
| color_description | TEXT | Y | N | N | Färbung/Grundfärbung |
| special_features | TEXT | Y | N | N | Besondere Merkmale |
| gender_differences | TEXT | Y | N | N | Geschlechtsunterschiede |
| generations_per_year | INT | N | N | Y | 1-4 (oder mehr) |
| hibernation_stage | ENUM | N | N | N | egg/larva/pupa/imago |
| pupal_duration_days | INT | Y | N | N | Raupenentwicklung (Tage) |
| red_list_status_de | VARCHAR(50) | Y | N | N | Deutschland Rote Liste |
| red_list_status_eu | VARCHAR(50) | Y | N | N | EU Rote Liste |
| abundance_trend | VARCHAR(100) | Y | N | N | z.B. "stabil", "rückläufig" |
| protection_status | ENUM | N | N | N | none/national/european |
| created_at | TIMESTAMP | N | N | N | Laravel timestamp |
| updated_at | TIMESTAMP | N | N | N | Laravel timestamp |

**Eloquent Model**:
```php
class Species extends Model
{
    protected $fillable = [
        'family_id', 'name', 'scientific_name', 'size_category',
        'color_description', 'special_features', 'gender_differences',
        'generations_per_year', 'hibernation_stage', 'pupal_duration_days',
        'red_list_status_de', 'red_list_status_eu', 'abundance_trend',
        'protection_status'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function family() { return $this->belongsTo(Family::class); }
    public function distributions() { return $this->belongsToMany(DistributionArea::class, 'species_distribution'); }
    public function habitats() { return $this->belongsToMany(Habitat::class, 'species_habitat'); }
    public function hostPlants() { return $this->belongsToMany(Plant::class, 'species_plant'); }
}
```

**Validierung**:
- name: required|string|max:255
- scientific_name: required|string|max:255
- family_id: required|exists:families,id
- size_category: required|in:XS,S,M,L,XL
- generations_per_year: required|integer|min:1|max:4
- hibernation_stage: required|in:egg,larva,pupa,imago
- protection_status: required|in:none,national,european

**Relationships** (M2M):
- distributions: Mit DistributionArea (many-to-many über Pivot-Tabelle)
- habitats: Mit Habitat (many-to-many über Pivot-Tabelle)
- hostPlants: Mit Plant (many-to-many über Pivot-Tabelle)

---

### 4. Habitat (Lebensraum mit Hierarchie)

**Table**: `habitats`

**Columns**:
| Column | Type | Nullable | Unique | Index | Notes |
|--------|------|----------|--------|-------|-------|
| id | BIGINT | N | Y | PK | Auto-increment |
| user_id | BIGINT | N | N | Y | FK → users |
| parent_id | BIGINT | Y | N | Y | Self-reference (für Hierarchie) |
| name | VARCHAR(255) | N | N | Y | z.B. "Wald" → "Laubwald" |
| description | TEXT | Y | N | N | Optional |
| level | INT | N | N | Y | 1=Oberkategorie, 2=Unterkategorie |
| created_at | TIMESTAMP | N | N | N | Laravel timestamp |
| updated_at | TIMESTAMP | N | N | N | Laravel timestamp |

**Eloquent Model**:
```php
class Habitat extends Model
{
    protected $fillable = ['parent_id', 'name', 'description', 'level'];

    public function user() { return $this->belongsTo(User::class); }
    public function parent() { return $this->belongsTo(Habitat::class, 'parent_id'); }
    public function children() { return $this->hasMany(Habitat::class, 'parent_id'); }
    public function species() { return $this->belongsToMany(Species::class, 'species_habitat'); }
    public function plants() { return $this->belongsToMany(Plant::class, 'plant_habitat'); }
}
```

**Validierung**:
- name: required|string|max:255
- parent_id: nullable|exists:habitats,id|different:id
- level: required|in:1,2

**Hierarchie-Logik**:
- Level 1 = Oberkategorie (parent_id = NULL)
- Level 2 = Unterkategorie (parent_id = ID von Level 1)

---

### 5. Plant (Pflanze/Raupenfutterpflanze)

**Table**: `plants`

**Columns**:
| Column | Type | Nullable | Unique | Index | Notes |
|--------|------|----------|--------|-------|-------|
| id | BIGINT | N | Y | PK | Auto-increment |
| user_id | BIGINT | N | N | Y | FK → users |
| life_form_id | BIGINT | N | N | Y | FK → life_forms |
| name | VARCHAR(255) | N | Y | Y | Deutscher Name |
| scientific_name | VARCHAR(255) | N | Y | Y | Lateinischer Name |
| family_genus | VARCHAR(255) | Y | N | N | Familie/Gattung |
| light_number | INT | N | N | Y | Lichtzahl 1-9 |
| temperature_number | INT | N | N | Y | Temperaturzahl 1-9 |
| continentality_number | INT | N | N | Y | Kontinentalitätszahl 1-9 |
| reaction_number | INT | N | N | Y | Reaktionszahl (pH) 1-9 |
| moisture_number | INT | N | N | Y | Feuchtezahl 1-9 |
| moisture_variation | VARCHAR(100) | Y | N | N | Feuchtewechsel Toleranz |
| nitrogen_number | INT | N | N | Y | Stickstoffzahl 1-9 |
| bloom_months | VARCHAR(100) | Y | N | N | JSON: [1,2,3...12] oder Text |
| bloom_color | VARCHAR(100) | Y | N | N | z.B. "weiß", "gelb", "rosa" |
| plant_height_cm | INT | Y | N | N | Maximale Höhe in cm |
| lifespan | ENUM | N | N | Y | annual/biennial/perennial |
| location | VARCHAR(255) | Y | N | N | Standort (sonnig, halbschatten, etc.) |
| is_native | BOOLEAN | N | N | Y | Einheimisch (ja/nein) |
| is_invasive | BOOLEAN | N | N | Y | Invasiv (ja/nein) |
| threat_status | TEXT | Y | N | N | Gefährdung/Bedrohung |
| persistence_organs | VARCHAR(100) | Y | N | N | z.B. "Samen", "Zwiebel", "Rhizom" |
| created_at | TIMESTAMP | N | N | N | Laravel timestamp |
| updated_at | TIMESTAMP | N | N | N | Laravel timestamp |

**Eloquent Model**:
```php
class Plant extends Model
{
    protected $fillable = [
        'life_form_id', 'name', 'scientific_name', 'family_genus',
        'light_number', 'temperature_number', 'continentality_number',
        'reaction_number', 'moisture_number', 'moisture_variation',
        'nitrogen_number', 'bloom_months', 'bloom_color', 'plant_height_cm',
        'lifespan', 'location', 'is_native', 'is_invasive', 'threat_status',
        'persistence_organs'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function lifeForm() { return $this->belongsTo(LifeForm::class); }
    public function habitats() { return $this->belongsToMany(Habitat::class, 'plant_habitat'); }
    public function hostForSpecies() { return $this->belongsToMany(Species::class, 'species_plant'); }
}
```

**Validierung**:
- name: required|string|max:255
- scientific_name: required|string|max:255
- life_form_id: required|exists:life_forms,id
- light_number: required|integer|min:1|max:9
- temperature_number: required|integer|min:1|max:9
- continentality_number: required|integer|min:1|max:9
- reaction_number: required|integer|min:1|max:9
- moisture_number: required|integer|min:1|max:9
- nitrogen_number: required|integer|min:1|max:9
- lifespan: required|in:annual,biennial,perennial
- is_native: required|boolean
- is_invasive: required|boolean

---

### 6. LifeForm (Lebensart/Lebensform)

**Table**: `life_forms`

**Columns**:
| Column | Type | Nullable | Unique | Index | Notes |
|--------|------|----------|--------|-------|-------|
| id | BIGINT | N | Y | PK | Auto-increment |
| user_id | BIGINT | N | N | Y | FK → users |
| name | VARCHAR(255) | N | Y | Y | z.B. "Baum", "Strauch", "Kraut" |
| description | TEXT | Y | N | N | Optional |
| examples | TEXT | Y | N | N | Optional Beispiele |
| created_at | TIMESTAMP | N | N | N | Laravel timestamp |
| updated_at | TIMESTAMP | N | N | N | Laravel timestamp |

**Eloquent Model**:
```php
class LifeForm extends Model
{
    protected $fillable = ['name', 'description', 'examples'];

    public function user() { return $this->belongsTo(User::class); }
    public function plants() { return $this->hasMany(Plant::class); }
}
```

**Validierung**:
- name: required|string|max:255|unique:life_forms
- description: nullable|string|max:1000
- examples: nullable|string|max:500

---

### 7. DistributionArea (Verbreitungsgebiet)

**Table**: `distribution_areas`

**Columns**:
| Column | Type | Nullable | Unique | Index | Notes |
|--------|------|----------|--------|-------|-------|
| id | BIGINT | N | Y | PK | Auto-increment |
| user_id | BIGINT | N | N | Y | FK → users |
| name | VARCHAR(255) | N | Y | Y | z.B. "Mitteleuropa" |
| description | TEXT | Y | N | N | Optional |
| created_at | TIMESTAMP | N | N | N | Laravel timestamp |
| updated_at | TIMESTAMP | N | N | N | Laravel timestamp |

**Eloquent Model**:
```php
class DistributionArea extends Model
{
    protected $fillable = ['name', 'description'];

    public function user() { return $this->belongsTo(User::class); }
    public function species() { return $this->belongsToMany(Species::class, 'species_distribution'); }
}
```

**Validierung**:
- name: required|string|max:255|unique:distribution_areas
- description: nullable|string|max:1000

---

## Pivot/Junction Tables

### species_distribution
Verbindungstabelle zwischen Species und DistributionArea (Many-to-Many)

**Columns**:
- id (PK)
- species_id (FK → species.id)
- distribution_area_id (FK → distribution_areas.id)
- created_at

---

### species_habitat
Verbindungstabelle zwischen Species und Habitat (Many-to-Many)

**Columns**:
- id (PK)
- species_id (FK → species.id)
- habitat_id (FK → habitats.id)
- created_at

---

### species_plant
Verbindungstabelle zwischen Species und Plant (Many-to-Many)

**Columns**:
- id (PK)
- species_id (FK → species.id)
- plant_id (FK → plants.id)
- plant_type | VARCHAR(50) | host/nectar/other - Typ der Pflanzenbeziehung
- created_at

---

### plant_habitat
Verbindungstabelle zwischen Plant und Habitat (Many-to-Many)

**Columns**:
- id (PK)
- plant_id (FK → plants.id)
- habitat_id (FK → habitats.id)
- created_at

---

## Database Indexes

**Critical Indexes for Performance**:

| Table | Column(s) | Type | Reason |
|-------|-----------|------|--------|
| species | family_id | FOREIGN | Filtering by family |
| species | size_category | INDEX | Filtering/Display |
| species | generations_per_year | INDEX | Filter by generation count |
| plants | light_number, temperature_number | INDEX | Combined filter for garden matching |
| plants | is_native, is_invasive | INDEX | Conservation filtering |
| plants | lifespan | INDEX | Garden planning |
| habitats | parent_id | FOREIGN | Hierarchical queries |
| habitats | level | INDEX | Quick filtering by level |

---

## Validation Rules Summary

**Critical Validations**:
1. ✅ Unique names (Family, Plant, LifeForm, DistributionArea)
2. ✅ Foreign key constraints (user_id, family_id, parent_id, life_form_id)
3. ✅ Range validations (1-9 scales for ecological values)
4. ✅ Enum validations (size_category, hibernation_stage, lifespan, etc.)
5. ✅ Circular reference protection (parent_id != id for hierarchies)
6. ✅ Duplicate prevention (name uniqueness per entity)

---

## Migration Strategy

All tables created via Laravel Migrations:
- `create_users_table.php`
- `create_families_table.php`
- `create_species_table.php`
- `create_habitats_table.php`
- `create_plants_table.php`
- `create_life_forms_table.php`
- `create_distribution_areas_table.php`
- `create_species_distribution_table.php` (pivot)
- `create_species_habitat_table.php` (pivot)
- `create_species_plant_table.php` (pivot)
- `create_plant_habitat_table.php` (pivot)

**Execution Order**: Migrations run in alphabetical order by default. Adjust timestamps to ensure:
1. Users created first
2. Base entities (Family, LifeForm, DistributionArea, Habitat) created
3. Species and Plant created (depend on base entities)
4. Pivot tables created last

---

## Next Phase

**Phase 1 Deliverables Complete**: ✅ data-model.md

**Remaining Phase 1 Tasks**:
- [ ] contracts/ (OpenAPI API Documentation)
- [ ] quickstart.md (Setup and Getting Started)
- [ ] Update agent context for Laravel/PHP

