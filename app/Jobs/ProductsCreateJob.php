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

class ProductsCreateJob implements ShouldQueue
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
                'description' => $productData->body_html ?? '', // Webhook uses body_html usually
                'vendor' => $productData->vendor,
                'product_type' => $productData->product_type,
                'status' => strtoupper($productData->status), // Webhook status might be lowercase
                'tags' => $productData->tags, // Comma separated string in REST
                'images' => $productData->images,
                'variants' => $productData->variants,
            ]
        );

        Log::info("Product created/updated via webhook: {$productData->id} for {$this->shopDomain}");
    }
}
