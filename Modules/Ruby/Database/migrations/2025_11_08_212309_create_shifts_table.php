<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('r_shifts', function(Blueprint $table){
            $table->increments('id');
            $table->string('start', 5);
            $table->string('end', 5);
        });

        Schema::create('r_contact_shifts', function(Blueprint $table){
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('shift_id');
            $table->unsignedTinyInteger('weekday');
            $table->primary(['contact_id', 'shift_id', 'weekday']);
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('r_shifts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('r_contact_shifts');
        Schema::dropIfExists('r_shifts');
    }
};
