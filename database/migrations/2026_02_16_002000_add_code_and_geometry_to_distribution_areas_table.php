<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_areas', function (Blueprint $table) {
            $table->string('code', 120)->nullable()->after('name');
            $table->json('geometry_geojson')->nullable()->after('description');
            $table->index('code');
        });

        $areas = DB::table('distribution_areas')->select('id', 'name')->get();
        $usedCodes = [];

        foreach ($areas as $area) {
            $base = Str::slug((string) $area->name);
            if ($base === '') {
                $base = 'area';
            }

            $code = $base;
            if (isset($usedCodes[$code])) {
                $code = $base . '-' . $area->id;
            }

            $usedCodes[$code] = true;

            DB::table('distribution_areas')
                ->where('id', $area->id)
                ->update(['code' => $code]);
        }

        Schema::table('distribution_areas', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_areas', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropIndex(['code']);
            $table->dropColumn(['code', 'geometry_geojson']);
        });
    }
};
