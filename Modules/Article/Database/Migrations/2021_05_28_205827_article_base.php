<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ArticleBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->create('la_articles', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedTinyInteger('type');
            $table->timestamps();
        });

        Schema::connection('tenant')->create('la_article_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('locale', 2);
        });

        Schema::connection('tenant')->create('la_categories', function(Blueprint $table){
            $table->increments('id');
            $table->timestamps();
        });

        Schema::connection('tenant')->create('la_category_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->foreign('category_id')->references('id')->on('la_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('locale', 2);
        });

        Schema::connection('tenant')->create('la_article_categories', function(Blueprint $table){
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('category_id');
            $table->primary(['article_id', 'category_id']);
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('la_categories')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::connection('tenant')->create('la_properties', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedTinyInteger('type');
            $table->boolean('public')->default(true);
            $table->boolean('system')->default(false);
        });

        Schema::connection('tenant')->create('la_property_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('property_id');
            $table->foreign('property_id')->references('id')->on('la_properties')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('locale', 2);
        });

        Schema::connection('tenant')->create('la_options', function(Blueprint $table){
            $table->increments('id');
        });

        Schema::connection('tenant')->create('la_option_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('option_id');
            $table->foreign('option_id')->references('id')->on('la_options')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('locale', 2);
        });

        Schema::connection('tenant')->create('la_property_options', function(Blueprint $table){
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('option_id');
            $table->primary(['property_id', 'option_id']);
            $table->foreign('property_id')->references('id')->on('la_properties')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('la_options')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::connection('tenant')->dropIfExists('la_property_options');
        Schema::connection('tenant')->dropIfExists('la_option_locales');
        Schema::connection('tenant')->dropIfExists('la_options');
        Schema::connection('tenant')->dropIfExists('la_property_locales');
        Schema::connection('tenant')->dropIfExists('la_properties');
        Schema::connection('tenant')->dropIfExists('la_article_categories');
        Schema::connection('tenant')->dropIfExists('la_category_locales');
        Schema::connection('tenant')->dropIfExists('la_categories');
        Schema::connection('tenant')->dropIfExists('la_article_locales');
        Schema::connection('tenant')->dropIfExists('la_articles');
    }
}
