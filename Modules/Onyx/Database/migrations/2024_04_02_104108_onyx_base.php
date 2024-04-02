<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('o_purchases', function(Blueprint $table){
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

        Schema::create('o_purchase_lines', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('o_purchases')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
            $table->float('price');
        });

        Schema::create('o_purchase_histories', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('o_purchases')->onUpdate('cascade')->onDelete('cascade');
            $table->text('event');
            $table->timestamp('time');
            $table->timestamp('created_at');
        });

        Schema::create('o_product_plans', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('s_users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
        });

        Schema::create('o_plan_consumptions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('product_plan_id');
            $table->foreign('product_plan_id')->references('id')->on('o_product_plans')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
            $table->timestamps();
        });

        Schema::create('o_plan_executions', function(Blueprint $table){
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

        Schema::create('o_execution_consumptions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('plan_execution_id');
            $table->foreign('plan_execution_id')->references('id')->on('o_plan_executions')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id')->nullable();
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('set null');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
            $table->timestamps();
        });

        Schema::create('o_execution_logs', function(Blueprint $table){
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
     */
    public function down(): void{
        Schema::dropIfExists('o_execution_logs');
        Schema::dropIfExists('o_execution_consumptions');
        Schema::dropIfExists('o_plan_executions');
        Schema::dropIfExists('o_plan_consumptions');
        Schema::dropIfExists('o_product_plans');
        Schema::dropIfExists('o_purchase_histories');
        Schema::dropIfExists('o_purchase_lines');
        Schema::dropIfExists('o_purchases');
    }
};
