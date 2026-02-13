<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('threat_categories', function (Blueprint $table) {
            if (Schema::hasColumn('threat_categories', 'description')) {
                $table->string('description')->nullable()->change();
            }
            if (Schema::hasColumn('threat_categories', 'rank')) {
                $table->dropUnique('threat_categories_rank_unique');
            }
        });
    }

    public function down(): void
    {
    }
};
