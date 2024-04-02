<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('la_articles', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedTinyInteger('type');
            $table->timestamps();
        });

        Schema::create('la_article_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('locale', 2);
        });

        Schema::create('la_categories', function(Blueprint $table){
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('la_category_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->foreign('category_id')->references('id')->on('la_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('locale', 2);
        });

        Schema::create('la_article_categories', function(Blueprint $table){
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('category_id');
            $table->primary(['article_id', 'category_id']);
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('la_categories')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('la_properties', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedTinyInteger('type');
            $table->boolean('public')->default(true);
            $table->boolean('system')->default(false);
        });

        Schema::create('la_property_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('property_id');
            $table->foreign('property_id')->references('id')->on('la_properties')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('locale', 2);
        });

        Schema::create('la_options', function(Blueprint $table){
            $table->increments('id');
        });

        Schema::create('la_option_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('option_id');
            $table->foreign('option_id')->references('id')->on('la_options')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('locale', 2);
        });

        Schema::create('la_property_options', function(Blueprint $table){
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('option_id');
            $table->primary(['property_id', 'option_id']);
            $table->foreign('property_id')->references('id')->on('la_properties')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('la_options')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('la_property_options');
        Schema::dropIfExists('la_option_locales');
        Schema::dropIfExists('la_options');
        Schema::dropIfExists('la_property_locales');
        Schema::dropIfExists('la_properties');
        Schema::dropIfExists('la_article_categories');
        Schema::dropIfExists('la_category_locales');
        Schema::dropIfExists('la_categories');
        Schema::dropIfExists('la_article_locales');
        Schema::dropIfExists('la_articles');
    }
};
