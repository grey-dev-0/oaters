<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('lc_measurement_units', function(Blueprint $table){
            $table->increments('id');
            $table->enum('type', ['length', 'area', 'volume', 'weight', 'data', 'box', 'piece']);
            $table->unsignedInteger('base_id')->nullable();
            $table->foreign('base_id')->references('id')->on('lc_measurement_units')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('factor', 20, 10)->default(1);
            $table->boolean('custom')->default(true);
            $table->softDeletes();
        });

        Schema::create('lc_measurement_unit_locales', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('measurement_unit_id');
            $table->foreign('measurement_unit_id')->references('id')->on('lc_measurement_units')->onUpdate('cascade')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name');
            $table->string('symbol', 10);
        });
    }

    public function down(): void{
        Schema::dropIfExists('lc_measurement_unit_locales');
        Schema::dropIfExists('lc_measurement_units');
    }
};
