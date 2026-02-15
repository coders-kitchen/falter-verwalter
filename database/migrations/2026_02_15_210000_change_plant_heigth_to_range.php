<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE plants SET plant_height_cm = 0 WHERE plant_height_cm IS NULL");

        Schema::table('plants', function (Blueprint $table) {            
            $table->integer('plant_height_cm_from');
            $table->integer('plant_height_cm_until');
        });
        
        DB::statement("UPDATE plants SET plant_height_cm_from = plant_height_cm, plant_height_cm_until = plant_height_cm");
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {            
            $table->drop('plant_height_cm_from');
            $table->drop('plant_height_cm_until');
        });
    }
};
