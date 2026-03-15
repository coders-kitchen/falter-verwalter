# Data Model Schema

- Generated at: 2026-03-15T08:11:13+00:00
- Source: `app/Models` + `database/migrations`
- Note: Static extraction from source files. Treat as a context snapshot.

## `cache`

- Migrations: `2026_02_18_140000_create_cache_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `expiration` | `integer` | no | `` | `` |
| `key` | `string` | no | `` | `` |
| `value` | `mediumText` | no | `` | `` |

## `cache_locks`

- Migrations: `2026_02_18_140000_create_cache_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `expiration` | `integer` | no | `` | `` |
| `key` | `string` | no | `` | `` |
| `owner` | `string` | no | `` | `` |

## `changelog_entries`

- Model: `ChangelogEntry`
- Migrations: `2026_02_24_220000_create_changelog_entries_table.php`, `2026_03_07_130000_add_split_details_to_changelog_entries_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `audience` | `enum(public, admin, both)` | no | `both` | `` |
| `commit_refs` | `json` | yes | `` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `details` | `longText` | yes | `` | `` |
| `details_admin` | `longText` | yes | `` | `` |
| `details_public` | `longText` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `is_active` | `boolean` | no | `true` | `` |
| `published_at` | `timestamp` | no | `` | `` |
| `summary` | `text` | no | `` | `` |
| `title` | `string` | no | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `version` | `string` | no | `` | `` |

Indexes:
- `unique` on `version`

## `distribution_areas`

- Model: `DistributionArea`
- Migrations: `2025_11_02_000002_create_distribution_areas_table.php`, `2026_02_16_002000_add_code_and_geometry_to_distribution_areas_table.php`, `2026_02_16_003000_add_geojson_path_to_distribution_areas_table.php`, `2026_02_16_004000_drop_geometry_geojson_from_distribution_areas_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `code` | `string(120)` | yes | `` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `description` | `text` | yes | `` | `` |
| `geojson_path` | `string` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `name` | `string` | no | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `unique` on `name`
- `index` on `name`
- `index` on `user_id`
- `index` on `code`
- `unique` on `code`
- `index` on `geojson_path`

## `families`

- Model: `Family`
- Migrations: `2025_11_02_000004_create_families_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `description` | `text` | yes | `` | `` |
| `genus` | `string(100)` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `name` | `string(100)` | no | `` | `` |
| `subfamily` | `string(100)` | yes | `` | `` |
| `tribe` | `string(100)` | yes | `` | `` |
| `type` | `enum(butterfly, plant)` | no | `butterfly` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `index` on `name`
- `index` on `user_id`
- `index` on `type`
- `unique` on `name`, `subfamily`, `genus`, `tribe`, `type`

## `genera`

- Model: `Genus`
- Migrations: `2026_02_16_001200_create_taxonomy_hierarchy_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `name` | `string(100)` | no | `` | `` |
| `subfamily_id` | `foreignId` | no | `` | `subfamilies.id` |
| `tribe_id` | `foreignId` | yes | `` | `tribes.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `subfamily_id`, `tribe_id`, `name`
- `index` on `name`

## `generations`

- Model: `Generation`
- Migrations: `2025_11_02_135000_create_generations_table.php`, `2025_11_02_135100_update_generations_separate_plant_types.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `description` | `text` | yes | `` | `` |
| `flight_end_month` | `integer` | no | `` | `` |
| `flight_start_month` | `integer` | no | `` | `` |
| `generation_number` | `integer` | no | `` | `` |
| `host_plants` | `json` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `larva_end_month` | `integer` | no | `` | `` |
| `larva_start_month` | `integer` | no | `` | `` |
| `larval_host_plants` | `json` | yes | `` | `` |
| `nectar_plants` | `json` | yes | `` | `` |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `index` on `species_id`, `generation_number`
- `index` on `user_id`
- `unique` on `species_id`, `generation_number`

## `habitats`

- Model: `Habitat`
- Migrations: `2025_11_02_000003_create_habitats_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `description` | `text` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `level` | `integer` | no | `1` | `` |
| `name` | `string` | no | `` | `` |
| `parent_id` | `foreignId` | yes | `` | `habitats.id` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `index` on `parent_id`, `level`
- `index` on `user_id`
- `unique` on `user_id`, `name`, `parent_id`

## `life_forms`

- Model: `LifeForm`
- Migrations: `2025_11_02_000001_create_life_forms_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `description` | `text` | yes | `` | `` |
| `examples` | `json` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `name` | `string` | no | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `unique` on `name`
- `index` on `name`
- `index` on `user_id`

## `plant_habitat`

- Migrations: `2025_11_02_000007_create_pivot_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `habitat_id` | `foreignId` | no | `` | `habitats.id` |
| `id` | `bigint` | no | `` | `` |
| `plant_id` | `foreignId` | no | `` | `plants.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `plant_id`, `habitat_id`

## `plants`

- Model: `Plant`
- Migrations: `2025_11_02_000005_create_plants_table.php`, `2025_11_02_140001_add_family_id_to_plants.php`, `2026_02_15_210000_change_plant_heigth_to_range.php`, `2026_02_15_210100_change_plant_bloom_month_range.php`, `2026_02_15_210200_change_plant_add_salt_number.php`, `2026_02_15_220000_drop_legacy_plant_columns.php`, `2026_02_15_230000_add_indicator_states_to_plants_table.php`, `2026_02_15_231000_add_heavy_metal_resistance_to_plants_table.php`, `2026_02_15_232000_add_threat_category_id_to_plants_table.php`, `2026_02_16_001200_create_taxonomy_hierarchy_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `bloom_color` | `string` | yes | `` | `` |
| `bloom_end_month` | `integer` | no | `` | `` |
| `bloom_start_month` | `integer` | no | `` | `` |
| `continentality_number` | `integer` | yes | `` | `` |
| `continentality_number_state` | `string(10)` | no | `numeric` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `family_genus` | `string` | yes | `` | `` |
| `family_id` | `foreignId` | yes | `` | `families.id` |
| `genus_id` | `foreignId` | yes | `` | `genera.id` |
| `heavy_metal_resistance` | `string` | no | `nicht schwermetallresistent` | `` |
| `id` | `bigint` | no | `` | `` |
| `is_invasive` | `boolean` | no | `false` | `` |
| `is_native` | `boolean` | no | `false` | `` |
| `life_form_id` | `foreignId` | no | `` | `life_forms.id` |
| `lifespan` | `enum(annual, biennial, perennial)` | yes | `` | `` |
| `light_number` | `integer` | yes | `` | `` |
| `light_number_state` | `string(10)` | no | `numeric` | `` |
| `location` | `text` | yes | `` | `` |
| `moisture_number` | `integer` | yes | `` | `` |
| `moisture_number_state` | `string(10)` | no | `numeric` | `` |
| `moisture_variation` | `integer` | yes | `` | `` |
| `moisture_variation_state` | `string(10)` | no | `numeric` | `` |
| `name` | `string` | no | `` | `` |
| `nitrogen_number` | `integer` | yes | `` | `` |
| `nitrogen_number_state` | `string(10)` | no | `numeric` | `` |
| `persistence_organs` | `text` | yes | `` | `` |
| `plant_height_cm_from` | `integer` | no | `` | `` |
| `plant_height_cm_until` | `integer` | no | `` | `` |
| `reaction_number` | `integer` | yes | `` | `` |
| `reaction_number_state` | `string(10)` | no | `numeric` | `` |
| `salt_number` | `integer` | yes | `` | `` |
| `salt_number_state` | `string(10)` | no | `numeric` | `` |
| `scientific_name` | `string` | yes | `` | `` |
| `temperature_number` | `integer` | yes | `` | `` |
| `temperature_number_state` | `string(10)` | no | `numeric` | `` |
| `threat_category_id` | `foreignId` | yes | `` | `threat_categories.id` |
| `threat_status` | `string` | yes | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `index` on `life_form_id`, `name`
- `index` on `user_id`
- `index` on `is_native`
- `index` on `is_invasive`
- `index` on `family_id`
- `index` on `genus_id`

## `regions`

- Migrations: `2025_11_02_170000_create_regions_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `code` | `string(10)` | no | `` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `description` | `text` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `name` | `string(255)` | no | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `code`
- `index` on `code`
- `index` on `name`

## `sessions`

- Migrations: `2025_11_02_113649_create_sessions_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `id` | `string` | no | `` | `` |
| `ip_address` | `string(45)` | yes | `` | `` |
| `last_activity` | `integer` | no | `` | `` |
| `payload` | `longText` | no | `` | `` |
| `user_agent` | `text` | yes | `` | `` |
| `user_id` | `foreignId` | yes | `` | `users.id` |

## `species`

- Model: `Species`
- Migrations: `2025_11_02_000006_create_species_table.php`, `2026_02_15_233000_add_sage_feeding_indicator_to_species_table.php`, `2026_02_16_001200_create_taxonomy_hierarchy_tables.php`, `2026_02_18_120000_update_species_special_features_and_drop_sage_indicator.php`, `2026_03_07_120000_move_phagy_levels_to_species_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `abundance_trend` | `string` | yes | `` | `` |
| `adult_phagy_level` | `enum(unbekannt, monophag, oligophag, polyphag)` | yes | `` | `` |
| `color_description` | `text` | yes | `` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `family_id` | `foreignId` | no | `` | `families.id` |
| `gender_differences` | `text` | yes | `` | `` |
| `generations_per_year` | `integer` | yes | `` | `` |
| `genus_id` | `foreignId` | yes | `` | `genera.id` |
| `hibernation_stage` | `enum(egg, larva, pupa, adult)` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `larval_phagy_level` | `enum(unbekannt, monophag, oligophag, polyphag)` | yes | `` | `` |
| `name` | `string` | no | `` | `` |
| `protection_status` | `string` | yes | `` | `` |
| `pupal_duration_days` | `integer` | yes | `` | `` |
| `red_list_status_de` | `string` | yes | `` | `` |
| `red_list_status_eu` | `string` | yes | `` | `` |
| `scientific_name` | `string` | yes | `` | `` |
| `size_category` | `enum(XS, S, M, L, XL)` | no | `` | `` |
| `special_features` | `string` | yes | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `index` on `family_id`, `size_category`
- `index` on `user_id`
- `index` on `name`
- `index` on `genus_id`

## `species_distribution`

- Migrations: `2025_11_02_000007_create_pivot_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `distribution_area_id` | `foreignId` | no | `` | `distribution_areas.id` |
| `id` | `bigint` | no | `` | `` |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `species_id`, `distribution_area_id`

## `species_distribution_areas`

- Model: `SpeciesDistributionArea`
- Migrations: `2026_02_08_180100_create_species_distribution_area_table.php`, `2026_02_08_180400_update_species_distribution_area_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `distribution_area_id` | `foreignId` | no | `` | `distribution_areas.id` |
| `id` | `bigint` | no | `` | `` |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `status` | `enum(heimisch, ausgestorben, neobiotisch)` | no | `heimisch` | `` |
| `threat_category_id` | `foreignId` | yes | `` | `threat_categories.id` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `unique` on `species_id`, `distribution_area_id`
- `index` on `species_id`, `status`
- `index` on `distribution_area_id`

## `species_endagered_status`

- Migrations: `2026_02_08_180300_create_species_endagered_status_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `distribution_area_id` | `foreignId` | no | `` | `distribution_areas.id` |
| `id` | `bigint` | no | `` | `` |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `threat_category_id` | `foreignId` | no | `` | `threat_categories.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `species_id`, `distribution_area_id`
- `index` on `species_id`, `threat_category_id`
- `index` on `distribution_area_id`

## `species_genus`

- Model: `SpeciesGenus`
- Migrations: `2026_02_24_110000_create_species_genus_table.php`, `2026_03_04_120000_add_phagy_levels_to_species_plant_and_species_genus_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `adult_phagy_level` | `enum(unbekannt, monophag, oligophag, polyphag)` | yes | `` | `` |
| `adult_preference` | `enum(primaer, sekundaer)` | yes | `` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `genus_id` | `foreignId` | no | `` | `genera.id` |
| `id` | `bigint` | no | `` | `` |
| `is_larval_host` | `boolean` | no | `false` | `` |
| `is_nectar` | `boolean` | no | `false` | `` |
| `larval_phagy_level` | `enum(unbekannt, monophag, oligophag, polyphag)` | yes | `` | `` |
| `larval_preference` | `enum(primaer, sekundaer)` | yes | `` | `` |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `species_id`, `genus_id`
- `index` on `species_id`
- `index` on `genus_id`

## `species_habitat`

- Migrations: `2025_11_02_000007_create_pivot_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `habitat_id` | `foreignId` | no | `` | `habitats.id` |
| `id` | `bigint` | no | `` | `` |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `species_id`, `habitat_id`

## `species_plant`

- Model: `SpeciesPlant`
- Migrations: `2025_11_02_000007_create_pivot_tables.php`, `2026_02_15_234000_refactor_species_plant_to_flags_and_migrate_generation_data.php`, `2026_02_24_100000_add_stage_preferences_to_species_plant_table.php`, `2026_03_04_120000_add_phagy_levels_to_species_plant_and_species_genus_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `adult_phagy_level` | `enum(unbekannt, monophag, oligophag, polyphag)` | yes | `` | `` |
| `adult_preference` | `enum(primaer, sekundaer)` | yes | `` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `is_larval_host` | `boolean` | no | `false` | `` |
| `is_nectar` | `boolean` | no | `false` | `` |
| `larval_phagy_level` | `enum(unbekannt, monophag, oligophag, polyphag)` | yes | `` | `` |
| `larval_preference` | `enum(primaer, sekundaer)` | yes | `` | `` |
| `plant_id` | `foreignId` | no | `` | `plants.id` |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `species_id`, `plant_id`

## `species_region`

- Migrations: `2025_11_02_170100_create_species_region_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `conservation_status` | `enum(nicht_gefährdet, gefährdet)` | no | `nicht_gefährdet` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `region_id` | `foreignId` | no | `` | `regions.id` |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `species_id`, `region_id`
- `index` on `species_id`, `conservation_status`
- `index` on `region_id`

## `species_tag`

- Migrations: `2026_03_07_140000_create_tags_and_species_tag_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `species_id` | `foreignId` | no | `` | `species.id` |
| `tag_id` | `foreignId` | no | `` | `tags.id` |

Indexes:
- `unique` on `species_id`, `tag_id`
- `index` on `tag_id`, `species_id`

## `subfamilies`

- Model: `Subfamily`
- Migrations: `2026_02_16_001200_create_taxonomy_hierarchy_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `family_id` | `foreignId` | no | `` | `families.id` |
| `id` | `bigint` | no | `` | `` |
| `name` | `string(100)` | no | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `family_id`, `name`
- `index` on `name`

## `tags`

- Model: `Tag`
- Migrations: `2026_03_07_140000_create_tags_and_species_tag_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `description` | `text` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `is_active` | `boolean` | no | `true` | `` |
| `name` | `string` | no | `` | `` |
| `slug` | `string` | no | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `name`
- `unique` on `slug`
- `index` on `is_active`

## `threat_categories`

- Model: `ThreatCategory`
- Migrations: `2026_02_08_180200_create_threat_category_table.php`, `2026_02_09_190000_update_threat_category_table.php`, `2026_02_13_200000_fix_threat_category_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `code` | `string(10)` | no | `` | `` |
| `color_code` | `string(7)` | no | `` | `` |
| `created_at` | `timestamp` | yes | `` | `` |
| `description` | `string` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `label` | `string(40)` | no | `` | `` |
| `rank` | `integer` | no | `` | `` |
| `threat_categories_rank_unique` | `dropUnique` | no | `` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |
| `user_id` | `foreignId` | no | `` | `users.id` |

Indexes:
- `unique` on `code`
- `unique` on `label`
- `unique` on `rank`
- `unique` on `code`, `label`
- `index` on `user_id`

## `tribes`

- Model: `Tribe`
- Migrations: `2026_02_16_001200_create_taxonomy_hierarchy_tables.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `name` | `string(100)` | no | `` | `` |
| `subfamily_id` | `foreignId` | no | `` | `subfamilies.id` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `subfamily_id`, `name`
- `index` on `name`

## `users`

- Model: `User`
- Migrations: `2025_11_02_000000_create_users_table.php`, `2026_02_24_220100_add_last_seen_changelog_version_to_users_table.php`

| Column | Type | Nullable | Default | References |
| --- | --- | --- | --- | --- |
| `created_at` | `timestamp` | yes | `` | `` |
| `email` | `string` | no | `` | `` |
| `email_verified_at` | `timestamp` | yes | `` | `` |
| `id` | `bigint` | no | `` | `` |
| `is_active` | `boolean` | no | `true` | `` |
| `last_login_at` | `timestamp` | yes | `` | `` |
| `last_seen_changelog_version` | `string` | yes | `` | `` |
| `name` | `string` | no | `` | `` |
| `password` | `string` | no | `` | `` |
| `remember_token` | `string` | yes | `` | `` |
| `role` | `enum(admin, viewer)` | no | `viewer` | `` |
| `updated_at` | `timestamp` | yes | `` | `` |

Indexes:
- `unique` on `email`
- `index` on `email`
- `index` on `role`

