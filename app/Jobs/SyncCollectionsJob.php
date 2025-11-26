<?php

namespace App\Jobs;

use App\Models\Collection;
use App\Models\User;
use App\Services\ShopifyGraphQLService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCollectionsJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new ShopifyGraphQLService($this->user);
        $cursor = null;
        $hasNextPage = true;

        while ($hasNextPage) {
            $data = $service->fetchCollections($cursor);
            $collectionsData = $data['collections'];

            foreach ($collectionsData['edges'] as $edge) {
                $node = $edge['node'];

                Collection::updateOrCreate(
                    [
                        'shop_id' => $this->user->id,
                        'shopify_id' => $node['id'],
                    ],
                    [
                        'title' => $node['title'],
                        'handle' => $node['handle'],
                        'description' => $node['description'],
                        'products_count' => $node['productsCount']['count'] ?? 0,
                        'sort_order' => $node['sortOrder'],
                    ]
                );
            }

            $pageInfo = $collectionsData['pageInfo'];
            $hasNextPage = $pageInfo['hasNextPage'];
            $cursor = $pageInfo['endCursor'];
        }

        $this->user->forceFill([
            'collections_last_synced_at' => now(),
        ])->save();

        Log::info("Synced collections for shop: {$this->user->name}");
    }
}
