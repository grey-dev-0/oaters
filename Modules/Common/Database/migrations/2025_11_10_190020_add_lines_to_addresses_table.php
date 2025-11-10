<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::table('lc_addresses', function(Blueprint $table){
            $table->text('line_1')->nullable();
            $table->text('line_2')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('long', 11, 8)->nullable();
        });
    }

    public function down(): void{
        Schema::table('lc_addresses', function(Blueprint $table){
            $table->dropColumn(['line_1', 'line_2', 'lat', 'long']);
        });
    }
};
