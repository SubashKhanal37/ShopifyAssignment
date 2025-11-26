<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use App\Services\ShopifyGraphQLService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductsJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new ShopifyGraphQLService($this->user);
        $cursor = null;
        $hasNextPage = true;

        while ($hasNextPage) {
            $data = $service->fetchProducts($cursor);
            $productsData = $data['products'];

            foreach ($productsData['edges'] as $edge) {
                $node = $edge['node'];

                Product::updateOrCreate(
                    [
                        'shop_id' => $this->user->id,
                        'shopify_id' => $node['id'],
                    ],
                    [
                        'title' => $node['title'],
                        'handle' => $node['handle'],
                        'description' => $node['description'],
                        'vendor' => $node['vendor'],
                        'product_type' => $node['productType'],
                        'status' => $node['status'],
                        'tags' => json_encode($node['tags']),
                        'images' => $node['images'],
                        'variants' => $node['variants'],
                    ]
                );
            }

            $pageInfo = $productsData['pageInfo'];
            $hasNextPage = $pageInfo['hasNextPage'];
            $cursor = $pageInfo['endCursor'];
        }

        $this->user->forceFill([
            'products_last_synced_at' => now(),
        ])->save();

        Log::info("Synced products for shop: {$this->user->name}");
    }
}
