<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::table('lc_addresses', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->renameColumn('country_id', 'city_id');
        });

        Schema::table('lc_addresses', function (Blueprint $table) {
            $table->foreign('city_id')->references('id')->on('lc_cities')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void{
        Schema::table('lc_addresses', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->renameColumn('city_id', 'country_id');
        });

        Schema::table('lc_addresses', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('lc_countries')->onUpdate('cascade')->onDelete('cascade');
        });
    }
};
