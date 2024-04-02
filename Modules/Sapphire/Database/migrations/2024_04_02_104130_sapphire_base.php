<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('s_users', function(Blueprint  $table){
            $table->increments('id');
            $table->unsignedInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('s_roles')->onUpdate('cascade')->onDelete('set null');
            $table->string('username');
            $table->string('password', 100);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('s_users');
    }
};
