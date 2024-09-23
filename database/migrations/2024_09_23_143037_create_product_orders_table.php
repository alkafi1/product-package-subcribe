<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('product_id'); 
            $table->integer('product_quantity'); 
            $table->decimal('product_price', 10, 2); 
            $table->string('purchase_type'); 
            $table->string('schedule_type')->nullable(); 
            $table->longText('purchase_type_details')->nullable(); 

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_orders');
    }
};
