<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create(config('currency.drivers.database.table'), function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('code', 10)->index();
            $table->string('symbol', 25);
            $table->string('format', 50);
            $table->string('exchange_rate');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('lc_countries', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('le_currencies')->onUpdate('cascade')->onDelete('set null');
            $table->string('code', 3);
            $table->boolean('status')->default(true);
        });

        Schema::create('lc_country_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')->references('id')->on('lc_countries')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('locale', 2);
        });

        Schema::create('lc_cities', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')->references('id')->on('lc_countries')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('status')->default(true);
        });

        Schema::create('lc_city_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('city_id');
            $table->foreign('city_id')->references('id')->on('lc_cities')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('locale', 2);
        });

        Schema::create('lc_timezones', function(Blueprint $table){
            $table->increments('id');
            $table->string('identifier');
        });

        Schema::create('lc_country_timezones', function(Blueprint $table){
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('timezone_id');
            $table->primary(['country_id', 'timezone_id']);
            $table->foreign('country_id')->references('id')->on('lc_countries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('timezone_id')->references('id')->on('lc_timezones')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('lc_contacts', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('timezone_id')->nullable();
            $table->foreign('timezone_id')->references('id')->on('lc_timezones')->onUpdate('cascade')->onDelete('set null');
            $table->string('name');
            $table->string('job');
            $table->string('image')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->unsignedTinyInteger('marital_status')->nullable();
            $table->date('birthdate')->nullable();
            $table->timestamps();
        });

        Schema::create('lc_phones', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->string('number', 16);
            $table->boolean('default')->default(false);
        });

        Schema::create('lc_emails', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->string('address');
            $table->boolean('default')->default(false);
        });

        Schema::create('lc_addresses', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')->references('id')->on('lc_countries')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('default')->default(false);
        });

        Schema::create('lc_colors', function(Blueprint $table){
            $table->string('id', 6)->primary();
        });

        Schema::create('lc_color_locales', function(Blueprint $table){
            $table->increments('id');
            $table->string('color_id', 6);
            $table->foreign('color_id')->references('id')->on('lc_colors')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('locale', 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('lc_color_locales');
        Schema::dropIfExists('lc_colors');
        Schema::dropIfExists('lc_addresses');
        Schema::dropIfExists('lc_emails');
        Schema::dropIfExists('lc_phones');
        Schema::dropIfExists('lc_contacts');
        Schema::dropIfExists('lc_country_timezones');
        Schema::dropIfExists('lc_timezones');
        Schema::dropIfExists('lc_country_locales');
        Schema::dropIfExists('lc_countries');
        Schema::dropIfExists(config('currency.drivers.database.table'));
    }
};
