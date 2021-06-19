<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OnyxBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->create('o_purchases', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('bank_account_id')->nullable();
            $table->foreign('bank_account_id')->references('id')->on('le_bank_accounts')->onUpdate('cascade')->onDelete('set null');
            $table->float('amount');
            $table->boolean('paid')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('o_purchase_lines', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('o_purchases')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
            $table->float('price');
        });

        Schema::connection('tenant')->create('o_purchase_histories', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('o_purchases')->onUpdate('cascade')->onDelete('cascade');
            $table->text('event');
            $table->timestamp('time');
            $table->timestamp('created_at');
        });

        Schema::connection('tenant')->create('o_product_plans', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
        });

        Schema::connection('tenant')->create('o_plan_consumptions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('product_plan_id');
            $table->foreign('product_plan_id')->references('id')->on('o_product_plans')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
            $table->timestamps();
        });

        Schema::connection('tenant')->create('o_plan_executions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('product_plan_id')->nullable();
            $table->foreign('product_plan_id')->references('id')->on('o_product_plans')->onUpdate('cascade')->onDelete('set null');
            $table->string('plan_log_id')->nullable();
            $table->unsignedTinyInteger('status');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('o_execution_consumptions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('plan_execution_id');
            $table->foreign('plan_execution_id')->references('id')->on('o_plan_executions')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id')->nullable();
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('set null');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
            $table->timestamps();
        });

        Schema::connection('tenant')->create('o_execution_logs', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('plan_execution_id');
            $table->foreign('plan_execution_id')->references('id')->on('o_plan_executions')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedTinyInteger('status');
            $table->text('note')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('o_execution_logs');
        Schema::connection('tenant')->dropIfExists('o_execution_consumptions');
        Schema::connection('tenant')->dropIfExists('o_plan_executions');
        Schema::connection('tenant')->dropIfExists('o_plan_consumptions');
        Schema::connection('tenant')->dropIfExists('o_product_plans');
        Schema::connection('tenant')->dropIfExists('o_purchase_histories');
        Schema::connection('tenant')->dropIfExists('o_purchase_lines');
        Schema::connection('tenant')->dropIfExists('o_purchases');
    }
}
