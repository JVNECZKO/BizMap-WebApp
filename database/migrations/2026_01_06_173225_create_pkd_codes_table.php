<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pkd_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 10);
            $table->string('name', 500);
            $table->string('version', 8)->default('2007');
            $table->string('parent_code', 10)->nullable()->index();
            $table->unsignedTinyInteger('level')->default(0);
            $table->boolean('is_leaf')->default(false);

            $table->timestamps();

            $table->unique(['code', 'version']);
            $table->index(['version', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkd_codes');
    }
};
