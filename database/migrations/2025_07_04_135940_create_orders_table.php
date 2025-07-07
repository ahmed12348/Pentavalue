<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

     public function up()
     {
         Schema::create('orders', function (Blueprint $table) {
             $table->id();
             $table->integer('product_id');
             $table->integer('quantity');
             $table->decimal('price', 10, 2);
             $table->datetime('date');
             $table->timestamps();
         });
     }
     
     public function down()
     {
         Schema::dropIfExists('orders');
     }
    /**
     * Reverse the migrations.
     */

};
