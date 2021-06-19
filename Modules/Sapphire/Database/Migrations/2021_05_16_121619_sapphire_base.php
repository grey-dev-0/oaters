<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SapphireBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->create('s_roles', function(Blueprint  $table){
            $table->increments('id');
        });

        Schema::connection('tenant')->create('s_role_locales', function(Blueprint  $table){
            $table->increments('id');
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on('s_roles')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->string('locale', 2);
        });

        Schema::connection('tenant')->create('s_users', function(Blueprint  $table){
            $table->increments('id');
            $table->unsignedInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('s_roles')->onUpdate('cascade')->onDelete('set null');
            $table->string('username');
            $table->string('password', 100);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('s_privileges', function(Blueprint  $table){
            $table->increments('id');
            $table->string('name');
        });

        Schema::connection('tenant')->create('s_privilege_locales', function(Blueprint  $table){
            $table->increments('id');
            $table->unsignedInteger('privilege_id');
            $table->foreign('privilege_id')->references('id')->on('s_privileges')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->string('locale', 2);
        });

        Schema::connection('tenant')->create('s_role_privileges', function(Blueprint  $table){
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('privilege_id');
            $table->primary(['role_id', 'privilege_id']);
            $table->foreign('role_id')->references('id')->on('s_roles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('privilege_id')->references('id')->on('s_privileges')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('s_role_privileges');
        Schema::connection('tenant')->dropIfExists('s_privilege_locales');
        Schema::connection('tenant')->dropIfExists('s_privileges');
        Schema::connection('tenant')->dropIfExists('s_users');
        Schema::connection('tenant')->dropIfExists('s_role_locales');
        Schema::connection('tenant')->dropIfExists('s_roles');
    }
}
