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
        Schema::create('products', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Basic product details
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->decimal('price', 8, 2);
            $table->integer('quantity')->default(0);

            // Bundle-specific columns
            $table->boolean('is_bundle')->default(false);
            $table->json('bundle_details')->nullable()->default(json_encode([])); // JSON for bundle details

            // Subscription-specific columns
            $table->boolean('is_subscribable')->default(false);
            $table->string('schedule_type')->nullable(); // 'monthly' or 'days'
            $table->json('schedule')->nullable()->default(json_encode([])); // Custom schedule in JSON format

            // Soft deletes (optional)
            $table->softDeletes(); // Allows soft deletion of products

            // Timestamps
            $table->timestamps();

            // Indexes for performance optimization
            $table->index('price');
            $table->index('is_bundle');
            $table->index('is_subscribable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
