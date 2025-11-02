# Quickstart: Admin-Bereich für Basis-Daten-Verwaltung

**Framework**: Laravel 12 + Eloquent ORM
**Frontend**: Vite + TailwindCSS + DaisyUI + Mary UI
**Database**: MySQL 8+
**Authentication**: Laravel Sanctum
**PHP Version**: 8.2+

---

## Installation & Setup

### 1. Prerequisites

Stellen Sie sicher, dass folgende Tools installiert sind:
- **PHP 8.2+** (Laravel 12 Anforderung)
- Composer
- Node.js 18+ und npm/yarn
- MySQL 8.0+
- Git

### 2. Clone Repository & Install Dependencies

```bash
# Clone das Repository
git clone https://github.com/falter-verwalter/falter-verwalter.git
cd falter-verwalter

# Backend Dependencies installieren
composer install

# Frontend Dependencies installieren
npm install
```

### 3. Environment Setup

```bash
# .env Datei erstellen (von .env.example kopieren)
cp .env.example .env

# APP_KEY generieren
php artisan key:generate

# Wichtige Einstellungen in .env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=falter_verwalter
DB_USERNAME=root
DB_PASSWORD=

# Sanctum Setup (bereits in Laravel 11 enthalten)
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
SESSION_DRIVER=cookie
```

### 4. Database Setup

```bash
# Migrations ausführen (erstellt alle Tabellen)
php artisan migrate

# Optional: Seeder für Test-Daten ausführen
php artisan db:seed
```

### 5. Start Development Server

**Terminal 1 - Backend (Laravel)**:
```bash
php artisan serve
# Server läuft auf http://localhost:8000
```

**Terminal 2 - Frontend (Vite)**:
```bash
npm run dev
# Vite Dev Server läuft auf http://localhost:5173
```

Öffnen Sie im Browser: **http://localhost:5173/admin**

---

## Project Structure

```
falter-verwalter/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── SpeciesController.php
│   │   │   ├── FamilyController.php
│   │   │   ├── HabitatController.php
│   │   │   ├── PlantController.php
│   │   │   ├── LifeFormController.php
│   │   │   └── DistributionAreaController.php
│   │   ├── Requests/
│   │   │   ├── SpeciesRequest.php
│   │   │   ├── PlantRequest.php
│   │   │   └── ...
│   │   └── Resources/
│   │       ├── SpeciesResource.php
│   │       ├── PlantResource.php
│   │       └── ...
│   └── Models/
│       ├── User.php
│       ├── Species.php
│       ├── Family.php
│       ├── Habitat.php
│       ├── Plant.php
│       ├── LifeForm.php
│       └── DistributionArea.php
├── database/
│   ├── migrations/
│   │   ├── 2025_01_XX_create_users_table.php
│   │   ├── 2025_01_XX_create_families_table.php
│   │   ├── 2025_01_XX_create_species_table.php
│   │   ├── 2025_01_XX_create_habitats_table.php
│   │   ├── 2025_01_XX_create_plants_table.php
│   │   ├── 2025_01_XX_create_life_forms_table.php
│   │   ├── 2025_01_XX_create_distribution_areas_table.php
│   │   └── 2025_01_XX_create_pivot_tables.php
│   └── seeders/
│       ├── UserSeeder.php
│       ├── LifeFormSeeder.php
│       └── DistributionAreaSeeder.php
├── resources/
│   ├── views/
│   │   └── admin.blade.php (SPA Entry Point)
│   ├── js/
│   │   ├── app.js
│   │   ├── components/
│   │   │   ├── SpeciesForm.vue
│   │   │   ├── FamilyManager.vue
│   │   │   ├── HabitatManager.vue
│   │   │   ├── PlantForm.vue
│   │   │   ├── LifeFormManager.vue
│   │   │   └── DistributionManager.vue
│   │   └── services/
│   │       ├── api.js (Axios client)
│   │       └── auth.js
│   └── css/
│       └── app.css
├── routes/
│   └── api.php (RESTful API Routes)
├── tests/
│   ├── Feature/
│   │   ├── SpeciesTest.php
│   │   ├── PlantTest.php
│   │   └── ...
│   └── Unit/
│       ├── Models/SpeciesTest.php
│       └── ...
├── vite.config.js
├── tailwind.config.js
└── package.json
```

---

## API Routes

Alle API Endpoints sind unter `/api/admin/` erreichbar.

```
# Authentication
POST   /api/admin/auth/login           - Admin Login
POST   /api/admin/auth/logout          - Admin Logout

# Families
GET    /api/admin/families             - Liste aller Familien
POST   /api/admin/families             - Neue Familie erstellen
GET    /api/admin/families/{id}        - Familie abrufen
PUT    /api/admin/families/{id}        - Familie bearbeiten
DELETE /api/admin/families/{id}        - Familie löschen

# Species
GET    /api/admin/species              - Liste aller Arten
POST   /api/admin/species              - Neue Art erstellen
GET    /api/admin/species/{id}         - Art abrufen
PUT    /api/admin/species/{id}         - Art bearbeiten
DELETE /api/admin/species/{id}         - Art löschen

# Habitats
GET    /api/admin/habitats             - Liste aller Habitate
POST   /api/admin/habitats             - Neues Habitat erstellen
GET    /api/admin/habitats/{id}        - Habitat abrufen
PUT    /api/admin/habitats/{id}        - Habitat bearbeiten
DELETE /api/admin/habitats/{id}        - Habitat löschen

# Plants
GET    /api/admin/plants               - Liste aller Pflanzen
POST   /api/admin/plants               - Neue Pflanze erstellen
GET    /api/admin/plants/{id}          - Pflanze abrufen
PUT    /api/admin/plants/{id}          - Pflanze bearbeiten
DELETE /api/admin/plants/{id}          - Pflanze löschen

# Life Forms
GET    /api/admin/life-forms           - Liste aller Lebensarten
POST   /api/admin/life-forms           - Neue Lebensart erstellen
GET    /api/admin/life-forms/{id}      - Lebensart abrufen
DELETE /api/admin/life-forms/{id}      - Lebensart löschen

# Distribution Areas
GET    /api/admin/distribution-areas   - Liste aller Verbreitungsgebiete
POST   /api/admin/distribution-areas   - Neues Gebiet erstellen
GET    /api/admin/distribution-areas/{id} - Gebiet abrufen
DELETE /api/admin/distribution-areas/{id} - Gebiet löschen
```

**Alle Endpoints benötigen Authentifizierung** (Sanctum Token/Session).

---

## Frontend Components

### TailwindCSS + DaisyUI + Mary UI Setup

```bash
# TailwindCSS ist bereits installiert
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# DaisyUI installieren
npm install daisyui@latest
```

**tailwind.config.js**:
```javascript
module.exports = {
  content: [
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.{js,jsx,ts,tsx,vue}",
  ],
  theme: {
    extend: {},
  },
  plugins: [require('daisyui')],
  daisyui: {
    themes: ['light', 'dark'],
  },
}
```

### Component Examples

**Species Form (Vue/Alpine.js)**:
```vue
<template>
  <form @submit.prevent="submitForm" class="space-y-4">
    <!-- Name Field -->
    <div class="form-control">
      <label class="label">
        <span class="label-text">Deutscher Name</span>
      </label>
      <input
        v-model="form.name"
        type="text"
        class="input input-bordered"
        required
      />
      <span v-if="errors.name" class="text-error">{{ errors.name[0] }}</span>
    </div>

    <!-- Size Category Dropdown -->
    <div class="form-control">
      <label class="label">
        <span class="label-text">Größenkategorie</span>
      </label>
      <select v-model="form.size_category" class="select select-bordered">
        <option value="XS">XS - sehr klein (&lt; 2,5 cm)</option>
        <option value="S">S - klein (2,5 - 3,5 cm)</option>
        <option value="M">M - mittelgroß (3,5 - 5 cm)</option>
        <option value="L">L - groß (5 - 6,5 cm)</option>
        <option value="XL">XL - sehr groß (≥ 6,5 cm)</option>
      </select>
    </div>

    <!-- Ecological Scale Input (1-9) -->
    <div class="form-control">
      <label class="label">
        <span class="label-text">Lichtzahl (1-9)</span>
      </label>
      <input
        v-model.number="form.light_number"
        type="range"
        min="1"
        max="9"
        class="range"
      />
      <span class="text-sm">{{ form.light_number }}</span>
    </div>

    <!-- Multi-Select für Habitate -->
    <div class="form-control">
      <label class="label">
        <span class="label-text">Habitate</span>
      </label>
      <select v-model="form.habitat_ids" multiple class="select select-bordered">
        <option v-for="habitat in habitats" :key="habitat.id" :value="habitat.id">
          {{ habitat.name }}
        </option>
      </select>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">
      Speichern
    </button>
  </form>
</template>

<script setup>
import { ref, reactive } from 'vue'
import api from '@/services/api'

const form = reactive({
  name: '',
  size_category: '',
  light_number: 5,
  habitat_ids: []
})

const errors = ref({})
const habitats = ref([])

// Load habitats on mount
onMounted(async () => {
  const res = await api.get('/habitats')
  habitats.value = res.data.data
})

const submitForm = async () => {
  try {
    await api.post('/species', form)
    alert('Schmetterlingsart erstellt!')
    // Reset form
  } catch (error) {
    errors.value = error.response.data.errors
  }
}
</script>
```

---

## Authentication Flow

### Login

```javascript
// Frontend: JavaScript/Axios
const response = await axios.post('/api/admin/auth/login', {
  email: 'admin@example.com',
  password: 'password123'
})

// Token automatisch in HttpOnly Cookie gespeichert (Sanctum)
// Axios sendet Cookie automatisch bei jedem Request
```

### API Request mit Authentifizierung

```javascript
// Sanctum: Cookie-basierte Authentifizierung (automatisch)
const response = await axios.get('/api/admin/species')
// Cookie wird automatisch in Header gesendet
```

---

## Database Seeding (für Tests)

```bash
# Default Seeder ausführen
php artisan db:seed

# Oder spezifischen Seeder ausführen
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=LifeFormSeeder
```

**UserSeeder.php** (erstellt Test-Admin):
```php
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.de',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
```

---

## Development Workflow

### 1. Backend Changes

```bash
# Neue Migration erstellen
php artisan make:migration create_species_table

# Model und Migration zusammen
php artisan make:model Species -m

# Controller erstellen
php artisan make:controller Api/SpeciesController --model=Species --resource
```

### 2. Frontend Changes

```bash
# Vite kompiliert automatisch bei Änderungen
# Im Browser: Auto-Reload durch HMR

# Production Build
npm run build
```

### 3. Testing

```bash
# PHPUnit Tests ausführen
php artisan test

# Oder mit Pest
php artisan pest
```

---

## Production Deployment

### Backend

```bash
# Composer optimieren
composer install --optimize-autoloader --no-dev

# Cache konfigurieren
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database Migrations
php artisan migrate --force

# Sanctum Setup
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Frontend

```bash
# Build für Production
npm run build

# Output in public/build/ (wird von Laravel serviert)
```

---

## Troubleshooting

### Common Issues

| Problem | Lösung |
|---------|--------|
| `CORS` Errors | Check `.env` SANCTUM_STATEFUL_DOMAINS |
| `Database Connection` Error | Verify DB credentials in `.env` |
| `401 Unauthorized` | Ensure token/session is set, run `php artisan migrate` |
| `500 Server Error` | Check `storage/logs/laravel.log` |
| `Vite CSS nicht loaded` | Stellen Sie sicher `npm run dev` läuft |

### Check Logs

```bash
# Laravel Logs
tail -f storage/logs/laravel.log

# Datenbank Verbindung testen
php artisan tinker
>>> DB::connection()->getPdo()
```

---

## Next Steps

1. **Local Development**: Führen Sie die oben genannten Setup-Schritte durch
2. **Create Admin User**: Verwenden Sie UserSeeder oder erstellen Sie manually
3. **Login & Test**: Öffnen Sie http://localhost:5173/admin
4. **Read API Documentation**: Siehe `contracts/openapi.yaml`
5. **Implement Features**: Folgen Sie den Task-List in `tasks.md`

---

## Resources

- [Laravel 11 Documentation](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Sanctum Authentication](https://laravel.com/docs/sanctum)
- [Vite Documentation](https://vitejs.dev)
- [TailwindCSS](https://tailwindcss.com)
- [DaisyUI Components](https://daisyui.com)

