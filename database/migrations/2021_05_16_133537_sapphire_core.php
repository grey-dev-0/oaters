<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SapphireCore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password', 100);
            $table->string('subdomain')->unique()->nullable();
            $table->string('hash');
            $table->boolean('main')->default(false);
            $table->timestamps();
        });

        Schema::create('subscriptions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
            $table->float('price');
            $table->integer('discount')->nullable();
            $table->enum('discount_type', ['fixed', 'percent']);
            $table->boolean('paid')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('modules',function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->float('price');
        });

        Schema::create('tenant_modules', function(Blueprint $table){
            $table->unsignedInteger('subscription_id');
            $table->unsignedInteger('module_id');
            $table->primary(['subscription_id', 'module_id']);
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('purchases', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('subscription_id');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount');
            $table->string('token')->nullable();
            $table->boolean('executed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('tenant_modules');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('tenants');
    }
}
