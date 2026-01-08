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
        Schema::create('businesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lp')->nullable()->index();
            $table->string('nip', 32)->nullable()->index();
            $table->string('regon', 32)->nullable()->index();
            $table->string('full_name', 191)->index();
            $table->string('slug', 191)->unique();

            $table->string('nazwisko', 120)->nullable();
            $table->string('imie', 120)->nullable();
            $table->string('telefon', 64)->nullable();
            $table->string('email', 191)->nullable()->index();
            $table->string('adres_www', 191)->nullable();

            $table->string('wojewodztwo', 150)->nullable()->index();
            $table->string('powiat', 150)->nullable()->index();
            $table->string('gmina', 150)->nullable()->index();
            $table->string('miejscowosc', 150)->nullable()->index();
            $table->string('ulica', 150)->nullable();
            $table->string('nr_budynku', 50)->nullable();
            $table->string('nr_lokalu', 50)->nullable();
            $table->string('kod_pocztowy', 20)->nullable()->index();

            $table->string('glowny_kod_pkd', 10)->nullable()->index();
            $table->text('pozostale_kody_pkd')->nullable();
            $table->string('rok_pkd', 10)->nullable();

            $table->string('status_dzialalnosci', 100)->nullable()->index();
            $table->date('data_rozpoczecia_dzialalnosci')->nullable()->index();
            $table->date('data_zakonczenia_dzialalnosci')->nullable()->index();
            $table->date('data_zawieszenia_dzialalnosci')->nullable()->index();
            $table->date('data_wznowienia_dzialalnosci')->nullable()->index();

            $table->timestamp('imported_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
