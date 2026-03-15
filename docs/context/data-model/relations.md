# Data Model Relations

- Generated at: 2026-03-15T08:11:13+00:00
- Source: Eloquent relation methods in `app/Models`

## `ChangelogEntry`

- Table: `changelog_entries`
- File: `app/Models/ChangelogEntry.php`
- Fillable: `version`, `title`, `summary`, `details`, `details_public`, `details_admin`, `audience`, `published_at`, `is_active`, `commit_refs`
- Casts: `published_at:datetime`, `is_active:boolean`, `commit_refs:array`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| _(none detected)_ |  |  |  |

## `DistributionArea`

- Table: `distribution_areas`
- File: `app/Models/DistributionArea.php`
- Fillable: `user_id`, `name`, `code`, `description`, `geojson_path`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `species` | `belongsToMany` | `Species` | `table=species_distribution_areas; pivot=status,threat_category_id,user_id` |

## `Family`

- Table: `families`
- File: `app/Models/Family.php`
- Fillable: `user_id`, `name`, `subfamily`, `genus`, `tribe`, `type`, `description`
- Casts: `type:string`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `species` | `hasMany` | `Species` |  |
| `plants` | `hasMany` | `Plant` | `fk=family_id` |
| `subfamilies` | `hasMany` | `Subfamily` |  |

## `Generation`

- Table: `generations`
- File: `app/Models/Generation.php`
- Fillable: `user_id`, `species_id`, `generation_number`, `larva_start_month`, `larva_end_month`, `flight_start_month`, `flight_end_month`, `host_plants`, `nectar_plants`, `larval_host_plants`, `description`
- Casts: `generation_number:integer`, `larva_start_month:integer`, `larva_end_month:integer`, `flight_start_month:integer`, `flight_end_month:integer`, `host_plants:array`, `nectar_plants:array`, `larval_host_plants:array`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `species` | `belongsTo` | `Species` |  |

## `Genus`

- Table: `genera`
- File: `app/Models/Genus.php`
- Fillable: `subfamily_id`, `tribe_id`, `name`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `subfamily` | `belongsTo` | `Subfamily` |  |
| `tribe` | `belongsTo` | `Tribe` |  |
| `species` | `hasMany` | `Species` |  |
| `plants` | `hasMany` | `Plant` |  |
| `assignedSpecies` | `belongsToMany` | `Species` | `table=species_genus; fk=genus_id; related=species_id; pivot=is_nectar,is_larval_host,adult_preference,larval_preference` |

## `Habitat`

- Table: `habitats`
- File: `app/Models/Habitat.php`
- Fillable: `user_id`, `parent_id`, `name`, `description`, `level`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `parent` | `belongsTo` | `Habitat` | `fk=parent_id` |
| `children` | `hasMany` | `Habitat` | `fk=parent_id` |
| `species` | `belongsToMany` | `Species` | `table=species_habitat` |
| `plants` | `belongsToMany` | `Plant` | `table=plant_habitat` |

## `LifeForm`

- Table: `life_forms`
- File: `app/Models/LifeForm.php`
- Fillable: `user_id`, `name`, `description`, `examples`
- Casts: `examples:json`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `plants` | `hasMany` | `Plant` |  |

## `Plant`

- Table: `plants`
- File: `app/Models/Plant.php`
- Fillable: `user_id`, `life_form_id`, `family_id`, `genus_id`, `threat_category_id`, `name`, `scientific_name`, `family_genus`, `light_number`, `light_number_state`, `salt_number`, `salt_number_state`, `temperature_number`, `temperature_number_state`, `continentality_number`, `continentality_number_state`, `reaction_number`, `reaction_number_state`, `moisture_number`, `moisture_number_state`, `moisture_variation`, `moisture_variation_state`, `nitrogen_number`, `nitrogen_number_state`, `bloom_start_month`, `bloom_end_month`, `bloom_color`, `plant_height_cm_from`, `plant_height_cm_until`, `lifespan`, `location`, `is_native`, `is_invasive`, `threat_status`, `heavy_metal_resistance`, `persistence_organs`
- Casts: `is_native:boolean`, `is_invasive:boolean`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `lifeForm` | `belongsTo` | `LifeForm` |  |
| `family` | `belongsTo` | `Family` |  |
| `genus` | `belongsTo` | `Genus` |  |
| `threatCategory` | `belongsTo` | `ThreatCategory` |  |
| `habitats` | `belongsToMany` | `Habitat` | `table=plant_habitat` |
| `speciesAsHostPlant` | `belongsToMany` | `Species` | `table=species_plant; fk=plant_id; related=species_id; pivot=is_nectar,is_larval_host,adult_preference,larval_preference` |

## `Species`

- Table: `species`
- File: `app/Models/Species.php`
- Fillable: `user_id`, `family_id`, `genus_id`, `name`, `scientific_name`, `size_category`, `color_description`, `special_features`, `gender_differences`, `generations_per_year`, `hibernation_stage`, `adult_phagy_level`, `larval_phagy_level`, `pupal_duration_days`, `red_list_status_de`, `red_list_status_eu`, `abundance_trend`, `protection_status`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `family` | `belongsTo` | `Family` |  |
| `genus` | `belongsTo` | `Genus` |  |
| `generations` | `hasMany` | `Generation` |  |
| `distributionAreas` | `belongsToMany` | `DistributionArea` | `table=species_distribution_areas; pivot=status,threat_category_id,user_id` |
| `habitats` | `belongsToMany` | `Habitat` | `table=species_habitat` |
| `plants` | `belongsToMany` | `Plant` | `table=species_plant; fk=species_id; related=plant_id; pivot=is_nectar,is_larval_host,adult_preference,larval_preference` |
| `plantGenera` | `belongsToMany` | `Genus` | `table=species_genus; fk=species_id; related=genus_id; pivot=is_nectar,is_larval_host,adult_preference,larval_preference` |
| `tags` | `belongsToMany` | `Tag` | `table=species_tag; fk=species_id; related=tag_id` |

## `SpeciesDistributionArea`

- Table: `species_distribution_areas`
- File: `app/Models/SpeciesDistributionArea.php`
- Fillable: `species_id`, `distribution_area_id`, `status`, `threat_category_id`, `user_id`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `species` | `belongsTo` | `Species` |  |
| `distributionArea` | `belongsTo` | `DistributionArea` |  |
| `threatCategory` | `belongsTo` | `ThreatCategory` |  |

## `SpeciesGenus`

- Table: `species_genus`
- File: `app/Models/SpeciesGenus.php`
- Fillable: `species_id`, `genus_id`, `is_nectar`, `is_larval_host`, `adult_preference`, `larval_preference`
- Casts: `is_nectar:boolean`, `is_larval_host:boolean`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `species` | `belongsTo` | `Species` |  |
| `genus` | `belongsTo` | `Genus` |  |

## `SpeciesPlant`

- Table: `species_plant`
- File: `app/Models/SpeciesPlant.php`
- Fillable: `species_id`, `plant_id`, `is_nectar`, `is_larval_host`, `adult_preference`, `larval_preference`
- Casts: `is_nectar:boolean`, `is_larval_host:boolean`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `species` | `belongsTo` | `Species` |  |
| `plant` | `belongsTo` | `Plant` |  |

## `Subfamily`

- Table: `subfamilies`
- File: `app/Models/Subfamily.php`
- Fillable: `family_id`, `name`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `family` | `belongsTo` | `Family` |  |
| `tribes` | `hasMany` | `Tribe` |  |
| `genera` | `hasMany` | `Genus` |  |

## `Tag`

- Table: `tags`
- File: `app/Models/Tag.php`
- Fillable: `name`, `slug`, `description`, `is_active`
- Casts: `is_active:boolean`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `species` | `belongsToMany` | `Species` | `table=species_tag; fk=tag_id; related=species_id` |

## `ThreatCategory`

- Table: `threat_categories`
- File: `app/Models/ThreatCategory.php`
- Fillable: `code`, `label`, `description`, `rank`, `color_code`, `user_id`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `speciesDistributionAreas` | `hasMany` | `SpeciesDistributionArea` |  |
| `plants` | `hasMany` | `Plant` |  |

## `Tribe`

- Table: `tribes`
- File: `app/Models/Tribe.php`
- Fillable: `subfamily_id`, `name`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| `subfamily` | `belongsTo` | `Subfamily` |  |
| `genera` | `hasMany` | `Genus` |  |

## `User`

- Table: `users`
- File: `app/Models/User.php`
- Fillable: `name`, `email`, `password`, `role`, `is_active`, `last_seen_changelog_version`
- Casts: `email_verified_at:datetime`, `is_active:boolean`, `last_seen_changelog_version:string`

| Method | Relation | Target | Details |
| --- | --- | --- | --- |
| _(none detected)_ |  |  |  |

