<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('le_bank_accounts', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('cascade');
            $table->string('bank');
            $table->string('name');
            $table->string('number');
            $table->string('iban')->nullable();
            $table->string('swift');
            $table->timestamps();
        });

        Schema::create('le_orders', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('lc_contacts')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('shipping_address_id')->nullable();
            $table->foreign('shipping_address_id')->references('id')->on('lc_addresses')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('billing_address_id')->nullable();
            $table->foreign('billing_address_id')->references('id')->on('lc_addresses')->onUpdate('cascade')->onDelete('set null');
            $table->float('amount');
            $table->boolean('paid')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('le_order_lines', function(Blueprint  $table){
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('le_orders')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('la_articles')->onUpdate('cascade')->onDelete('cascade');
            $table->float('quantity');
            $table->string('quantity_unit', 10);
            $table->float('price');
            $table->float('discount');
            $table->enum('discount_type', ['fixed', 'percent']);
        });

        Schema::create('le_refunds', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('refundable_id');
            $table->string('refundable_type', 30);
            $table->index(['refundable_type', 'refundable_id'], 'refundable_morph');
            $table->float('amount');
            $table->boolean('paid')->default(false);
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('le_order_histories', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')->references('id')->on('le_orders')->onUpdate('cascade')->onDelete('cascade');
            $table->text('event');
            $table->timestamp('time');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('le_transactions', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('transferable_id');
            $table->string('transferable_type', 30);
            $table->index(['transferable_type', 'transferable_id'], 'transferable_morph');
            $table->enum('type', ['debit', 'credit']);
            $table->float('amount');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('le_transactions');
        Schema::dropIfExists('le_order_histories');
        Schema::dropIfExists('le_refunds');
        Schema::dropIfExists('le_order_lines');
        Schema::dropIfExists('le_orders');
        Schema::dropIfExists('le_bank_accounts');
    }
};
