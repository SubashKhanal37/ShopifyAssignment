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

class ProductsUpdateJob implements ShouldQueue
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

        Product::updateOrCreate(
            [
                'shop_id' => $shop->id,
                'shopify_id' => $shopifyId,
            ],
            [
                'title' => $productData->title,
                'handle' => $productData->handle,
                'description' => $productData->body_html ?? '',
                'vendor' => $productData->vendor,
                'product_type' => $productData->product_type,
                'status' => strtoupper($productData->status),
                'tags' => $productData->tags,
                'images' => $productData->images,
                'variants' => $productData->variants,
            ]
        );

        Log::info("Product updated via webhook: {$productData->id} for {$this->shopDomain}");
    }
}
