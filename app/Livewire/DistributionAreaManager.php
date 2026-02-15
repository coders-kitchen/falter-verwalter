<?php

namespace App\Livewire;

use App\Models\DistributionArea;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DistributionAreaManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $distributionArea = null;
    public $geojsonFile = null;
    public $removeGeojson = false;

    public $form = [
        'name' => '',
        'code' => '',
        'description' => '',
        'geojson_path' => null,
    ];

    protected function rules(): array
    {
        $distributionAreaId = $this->distributionArea?->id ?? 'NULL';

        return [
            'form.name' => 'required|string|max:255|unique:distribution_areas,name,' . $distributionAreaId,
            'form.code' => 'required|string|max:120|alpha_dash|unique:distribution_areas,code,' . $distributionAreaId,
            'form.description' => 'nullable|string',
            'geojsonFile' => 'nullable|file|mimes:json,geojson|max:5120',
            'removeGeojson' => 'boolean',
        ];
    }

    public function render()
    {
        $query = DistributionArea::query()
            ->select(['id', 'name', 'code', 'description', 'geojson_path']);

        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $items = $query->orderBy('name')
                       ->paginate(50);

        return view('livewire.distribution-area-manager', [
            'items' => $items,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(DistributionArea $distributionArea)
    {
        $this->distributionArea = $distributionArea;
        $this->form = [
            'name' => $distributionArea->name,
            'code' => $distributionArea->code,
            'description' => $distributionArea->description,
            'geojson_path' => $distributionArea->geojson_path,
        ];
        $this->removeGeojson = false;
        $this->showModal = true;
    }

    public function save()
    {
        if (blank($this->form['code'] ?? null) && !blank($this->form['name'] ?? null)) {
            $this->form['code'] = Str::slug((string) $this->form['name']);
        }

        $validated = $this->validate();
        $payload = $validated['form'];
        unset($payload['geojson_path']);

        if ($this->distributionArea && ($validated['removeGeojson'] ?? false)) {
            $this->deleteGeoJsonFile($this->distributionArea->geojson_path);
            $payload['geojson_path'] = null;
        }

        if ($this->geojsonFile) {
            $geometry = $this->resolveGeometryPayload($this->geojsonFile);
            $targetCode = $payload['code'] ?: ($this->distributionArea?->code ?? Str::slug((string) $payload['name']));
            $targetName = $payload['name'] ?: ($this->distributionArea?->name ?? null);
            $newGeojsonPath = $this->persistGeometryToFile($targetCode, $targetName, $geometry);

            if ($this->distributionArea && $this->distributionArea->geojson_path && $this->distributionArea->geojson_path !== $newGeojsonPath) {
                $this->deleteGeoJsonFile($this->distributionArea->geojson_path);
            }

            $payload['geojson_path'] = $newGeojsonPath;
        }

        if ($this->distributionArea) {
            $this->distributionArea->update($payload);
        } else {
            DistributionArea::create(array_merge($payload, ['user_id' => auth()->id()]));
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(DistributionArea $distributionArea)
    {
        $this->deleteGeoJsonFile($distributionArea->geojson_path);
        $distributionArea->delete();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->distributionArea = null;
        $this->form = [
            'name' => '',
            'code' => '',
            'description' => '',
            'geojson_path' => null,
        ];
        $this->geojsonFile = null;
        $this->removeGeojson = false;
        $this->resetErrorBag();
    }

    private function resolveGeometryPayload($geojsonFile): array
    {
        $raw = file_get_contents($geojsonFile->getRealPath());
        if ($raw === false) {
            throw ValidationException::withMessages([
                'geojsonFile' => 'Die GeoJSON-Datei konnte nicht gelesen werden.',
            ]);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages([
                'geojsonFile' => 'Die hochgeladene Datei enthält kein gültiges JSON.',
            ]);
        }

        return $this->extractGeometryFromGeoJson($decoded, 'geojsonFile');
    }

    private function extractGeometryFromGeoJson(array $decoded, string $field): array
    {
        $type = $decoded['type'] ?? null;

        if ($type === 'Feature') {
            $geometry = $decoded['geometry'] ?? null;
            if (!is_array($geometry)) {
                throw ValidationException::withMessages([
                    $field => 'GeoJSON Feature enthält keine gültige Geometry.',
                ]);
            }

            return $this->extractGeometryFromGeoJson($geometry, $field);
        }

        if ($type === 'FeatureCollection') {
            $features = $decoded['features'] ?? null;
            if (!is_array($features) || count($features) === 0) {
                throw ValidationException::withMessages([
                    $field => 'GeoJSON FeatureCollection enthält keine Features.',
                ]);
            }

            if (count($features) > 1) {
                throw ValidationException::withMessages([
                    $field => 'Bitte nur eine Region pro Datei hochladen (genau ein Feature).',
                ]);
            }

            if (!is_array($features[0])) {
                throw ValidationException::withMessages([
                    $field => 'Das Feature ist ungültig.',
                ]);
            }

            return $this->extractGeometryFromGeoJson($features[0], $field);
        }

        if (!in_array($type, ['Polygon', 'MultiPolygon'], true)) {
            throw ValidationException::withMessages([
                $field => 'Erlaubt sind nur GeoJSON Polygon oder MultiPolygon.',
            ]);
        }

        $coordinates = $decoded['coordinates'] ?? null;
        if (!is_array($coordinates) || count($coordinates) === 0) {
            throw ValidationException::withMessages([
                $field => 'Koordinaten fehlen oder sind leer.',
            ]);
        }

        return [
            'type' => $type,
            'coordinates' => $coordinates,
        ];
    }

    private function persistGeometryToFile(string $code, ?string $name, array $geometry): string
    {
        $safeCode = trim($code) !== '' ? $code : 'area';
        $filePath = 'distribution-areas/' . $safeCode . '.geojson';

        $payload = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [
                        'code' => $safeCode,
                        'name' => $name,
                    ],
                    'geometry' => $geometry,
                ],
            ],
        ];

        Storage::disk('public')->put($filePath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filePath;
    }

    private function deleteGeoJsonFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
