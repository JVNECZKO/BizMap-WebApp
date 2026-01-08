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
        Schema::create('business_pkd_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id')->index();
            $table->string('pkd_code', 10)->index();
            $table->string('pkd_version', 8)->default('2007')->index();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['business_id', 'pkd_code', 'pkd_version'], 'business_pkd_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_pkd_codes');
    }
};
