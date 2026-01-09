<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkd_taxonomies', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->string('subgroup_name');
            $table->string('group_slug')->index();
            $table->string('subgroup_slug')->index();
            $table->string('primary_code')->nullable()->index();
            $table->json('secondary_codes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkd_taxonomies');
    }
};
