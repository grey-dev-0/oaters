<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::table('o_purchase_lines', function(Blueprint $table){
            $table->dropColumn('quantity_unit');
        });
        
        Schema::table('o_purchase_lines', function(Blueprint $table){
            $table->unsignedInteger('quantity_unit_id')->after('quantity');
            $table->foreign('quantity_unit_id')->references('id')->on('lc_measurement_units')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('o_product_plans', function(Blueprint $table){
            $table->dropColumn('quantity_unit');
        });
        
        Schema::table('o_product_plans', function(Blueprint $table){
            $table->unsignedInteger('quantity_unit_id');
            $table->foreign('quantity_unit_id')->references('id')->on('lc_measurement_units')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('o_plan_consumptions', function(Blueprint $table){
            $table->dropColumn('quantity_unit');
        });
        
        Schema::table('o_plan_consumptions', function(Blueprint $table){
            $table->unsignedInteger('quantity_unit_id')->after('quantity');
            $table->foreign('quantity_unit_id')->references('id')->on('lc_measurement_units')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('o_execution_consumptions', function(Blueprint $table){
            $table->dropColumn('quantity_unit');
        });
        
        Schema::table('o_execution_consumptions', function(Blueprint $table){
            $table->unsignedInteger('quantity_unit_id')->after('quantity');
            $table->foreign('quantity_unit_id')->references('id')->on('lc_measurement_units')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::table('o_execution_consumptions', function(Blueprint $table){
            $table->dropForeign(['quantity_unit_id']);
            $table->dropColumn('quantity_unit_id');
        });
        
        Schema::table('o_execution_consumptions', function(Blueprint $table){
            $table->string('quantity_unit', 10);
        });
        Schema::table('o_plan_consumptions', function(Blueprint $table){
            $table->dropForeign(['quantity_unit_id']);
            $table->dropColumn('quantity_unit_id');
        });
        
        Schema::table('o_plan_consumptions', function(Blueprint $table){
            $table->string('quantity_unit', 10);
        });
        Schema::table('o_product_plans', function(Blueprint $table){
            $table->dropForeign(['quantity_unit_id']);
            $table->dropColumn('quantity_unit_id');
        });
        
        Schema::table('o_product_plans', function(Blueprint $table){
            $table->string('quantity_unit', 10);
        });
        Schema::table('o_purchase_lines', function(Blueprint $table){
            $table->dropForeign(['quantity_unit_id']);
            $table->dropColumn('quantity_unit_id');
        });
        
        Schema::table('o_purchase_lines', function(Blueprint $table){
            $table->string('quantity_unit', 10);
        });
    }
};
