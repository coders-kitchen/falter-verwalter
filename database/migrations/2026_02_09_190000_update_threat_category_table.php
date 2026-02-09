<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('threat_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('threat_categories', 'color_code')) {
                $table->string('color_code', 7);
            }
        });
    }

    public function down(): void
    {
        Schema::table('threat_categories', function (Blueprint $table) {
            $table->dropColumn('color_code');
        });
    }
};
