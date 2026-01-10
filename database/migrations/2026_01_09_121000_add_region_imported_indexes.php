<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->index(['wojewodztwo', 'imported_at', 'id'], 'biz_woj_imported_idx');
            $table->index(['powiat', 'imported_at', 'id'], 'biz_pow_imported_idx');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex('biz_woj_imported_idx');
            $table->dropIndex('biz_pow_imported_idx');
        });
    }
};
