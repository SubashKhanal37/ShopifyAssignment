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
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('shopify_id')->unique(); // Shopify ID (e.g. gid://shopify/Product/12345)
            $table->string('title');
            $table->string('handle')->nullable();
            $table->text('description')->nullable();
            $table->string('vendor')->nullable();
            $table->string('product_type')->nullable();
            $table->string('status')->nullable(); // active, archived, draft
            $table->text('tags')->nullable(); // Stored as comma separated or JSON
            $table->json('images')->nullable();
            $table->json('variants')->nullable();
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('title');
            $table->index('status');
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
