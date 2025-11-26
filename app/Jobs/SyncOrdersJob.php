<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Services\ShopifyGraphQLService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncOrdersJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        $service = new ShopifyGraphQLService($this->user);
        $cursor = null;
        $hasNextPage = true;

        while ($hasNextPage) {
            $data = $service->fetchOrders($cursor);
            $ordersData = $data['orders'];

            foreach ($ordersData['edges'] as $edge) {
                $node = $edge['node'];

                $totalPrice = $node['totalPriceSet']['shopMoney']['amount'] ?? null;
                $currency = $node['totalPriceSet']['shopMoney']['currencyCode'] ?? null;

                Order::updateOrCreate(
                    [
                        'shop_id' => $this->user->id,
                        'shopify_id' => $node['id'],
                    ],
                    [
                        'name' => $node['name'] ?? null,
                        'financial_status' => null,
                        'fulfillment_status' => null,
                        'total_price' => $totalPrice,
                        'currency' => $currency,
                        'processed_at' => $node['processedAt'] ?? null,
                        'customer_name' => $node['customer']['displayName'] ?? null,
                        'customer_email' => $node['customer']['email'] ?? null,
                        'raw_payload' => $node,
                    ]
                );
            }

            $pageInfo = $ordersData['pageInfo'];
            $hasNextPage = $pageInfo['hasNextPage'];
            $cursor = $pageInfo['endCursor'];
        }

        $this->user->forceFill([
            'orders_last_synced_at' => now(),
        ])->save();

        Log::info("Synced orders for shop: {$this->user->name}");
    }
}
