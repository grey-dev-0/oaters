<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::table('a_cart_articles', function(Blueprint $table){
            $table->dropColumn('quantity_unit');
        });
        
        Schema::table('a_cart_articles', function(Blueprint $table){
            $table->unsignedInteger('quantity_unit_id');
            $table->foreign('quantity_unit_id')->references('id')->on('lc_measurement_units')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::table('a_cart_articles', function(Blueprint $table){
            $table->dropForeign(['quantity_unit_id']);
            $table->dropColumn('quantity_unit_id');
        });
        
        Schema::table('a_cart_articles', function(Blueprint $table){
            $table->string('quantity_unit', 10);
        });
    }
};
