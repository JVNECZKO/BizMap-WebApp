<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // przyspiesza zapytania filtrujące po statusie i sortujące po dacie importu
            $table->index(['status_dzialalnosci', 'imported_at', 'id'], 'biz_status_imported_idx');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex('biz_status_imported_idx');
        });
    }
};
