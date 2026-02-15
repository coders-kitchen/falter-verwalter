<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plants', function (Blueprint $table) {            
            $table->integer('bloom_start_month');
            $table->integer('bloom_end_month');
        });
    }

    public function down(): void
    {
        Schema::table('plants', function (Blueprint $table) {            
            $table->drop('bloom_start_month');
            $table->drop('bloom_end_month');
        });
    }
};
