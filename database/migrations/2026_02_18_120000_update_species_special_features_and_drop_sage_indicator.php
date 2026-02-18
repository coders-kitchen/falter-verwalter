<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('species', function (Blueprint $table) {
            $table->string('special_features')->nullable()->change();
            $table->dropColumn('sage_feeding_indicator');
        });
    }

    public function down(): void
    {
        Schema::table('species', function (Blueprint $table) {
            $table->text('special_features')->nullable()->change();
            $table->string('sage_feeding_indicator')->default('keine genaue Angabe');
        });
    }
};
