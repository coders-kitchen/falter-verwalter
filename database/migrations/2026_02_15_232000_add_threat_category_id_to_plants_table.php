<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->foreignId('threat_category_id')
                ->nullable()
                ->constrained('threat_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('threat_category_id');
        });
    }
};
