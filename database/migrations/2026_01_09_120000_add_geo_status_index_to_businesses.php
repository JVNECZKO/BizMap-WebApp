<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->index(['wojewodztwo', 'powiat', 'status_dzialalnosci', 'imported_at', 'id'], 'biz_geo_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex('biz_geo_status_idx');
        });
    }
};
