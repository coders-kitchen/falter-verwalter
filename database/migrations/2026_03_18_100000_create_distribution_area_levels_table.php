<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_area_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedInteger('sort_order')->default(100);
            $table->string('map_role', 32)->default('detail');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['map_role', 'sort_order']);
        });

        $now = now();

        DB::table('distribution_area_levels')->insert([
            [
                'name' => 'Hintergrund',
                'code' => 'background',
                'sort_order' => 10,
                'map_role' => 'background',
                'description' => 'Grobere Flaechen fuer Hintergrund-Layer, z. B. Bundeslaender.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Detail',
                'code' => 'detail',
                'sort_order' => 20,
                'map_role' => 'detail',
                'description' => 'Feinere Flaechen fuer Detail-Layer, z. B. Naturräume oder Teilregionen.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        Schema::table('distribution_areas', function (Blueprint $table) {
            $table->foreignId('distribution_area_level_id')
                ->nullable()
                ->after('user_id')
                ->constrained('distribution_area_levels')
                ->restrictOnDelete();

            $table->index(['distribution_area_level_id', 'name']);
        });

        $defaultLevelId = DB::table('distribution_area_levels')
            ->where('code', 'detail')
            ->value('id');

        if ($defaultLevelId !== null) {
            DB::table('distribution_areas')
                ->whereNull('distribution_area_level_id')
                ->update(['distribution_area_level_id' => $defaultLevelId]);
        }
    }

    public function down(): void
    {
        Schema::table('distribution_areas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('distribution_area_level_id');
        });

        Schema::dropIfExists('distribution_area_levels');
    }
};
