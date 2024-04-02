<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('a_carts', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique()->nullable();
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('token', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('a_cart_articles', function(Blueprint $table){
            $table->unsignedInteger('cart_id');
            $table->unsignedInteger('article_id');
            $table->primary(['cart_id', 'article_id']);
            $table->foreign('cart_id')->references('id')->on('a_carts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('a_cart_articles');
        Schema::dropIfExists('a_carts');
    }
};
