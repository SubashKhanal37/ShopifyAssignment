<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use stdClass;

class ProductsDeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $shopDomain,
        public stdClass $data
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shop = User::where('name', $this->shopDomain)->first();
        if (!$shop) {
            Log::warning("Shop not found for webhook: {$this->shopDomain}");
            return;
        }

        $productData = $this->data;
        $shopifyId = "gid://shopify/Product/{$productData->id}";

        Product::where('shop_id', $shop->id)
            ->where('shopify_id', $shopifyId)
            ->delete();

        Log::info("Product deleted via webhook: {$productData->id} for {$this->shopDomain}");
    }
}
