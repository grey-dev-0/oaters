<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::table('r_punches', function(Blueprint $table){
            $table->unsignedInteger('shift_id')->nullable()->after('contact_id');
            $table->smallInteger('latency')->nullable()->after('type');
            $table->foreign('shift_id', 'assigned_shift')->references('id')->on('r_shifts')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::table('r_punches', function(Blueprint $table){
            $table->dropForeign('assigned_shift');
            $table->dropColumn('latency');
            $table->dropColumn('shift_id');
        });
    }
};
