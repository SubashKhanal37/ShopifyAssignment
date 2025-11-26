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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('products_last_synced_at')->nullable()->after('remember_token');
            $table->timestamp('collections_last_synced_at')->nullable()->after('products_last_synced_at');
            $table->timestamp('orders_last_synced_at')->nullable()->after('collections_last_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'products_last_synced_at',
                'collections_last_synced_at',
                'orders_last_synced_at',
            ]);
        });
    }
};
