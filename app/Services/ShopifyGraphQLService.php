<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class ShopifyGraphQLService
{
    protected $shop;

    public function __construct(User $shop)
    {
        $this->shop = $shop;
    }

    public function fetchProducts($cursor = null, $limit = 10)
    {
        $query = <<<'GQL'
        query getProducts($first: Int!, $after: String) {
            products(first: $first, after: $after) {
                pageInfo {
                    hasNextPage
                    endCursor
                }
                edges {
                    node {
                        id
                        title
                        handle
                        description
                        vendor
                        productType
                        status
                        tags
                        images(first: 10) {
                            edges {
                                node {
                                    originalSrc
                                    altText
                                }
                            }
                        }
                        variants(first: 10) {
                            edges {
                                node {
                                    id
                                    title
                                    price
                                    sku
                                }
                            }
                        }
                    }
                }
            }
        }
GQL;

        return $this->execute($query, ['first' => $limit, 'after' => $cursor]);
    }

    public function fetchCollections($cursor = null, $limit = 10)
    {
        $query = <<<'GQL'
        query getCollections($first: Int!, $after: String) {
            collections(first: $first, after: $after) {
                pageInfo {
                    hasNextPage
                    endCursor
                }
                edges {
                    node {
                        id
                        title
                        handle
                        description
                        productsCount {
                            count
                        }
                        sortOrder
                    }
                }
            }
        }
GQL;

        return $this->execute($query, ['first' => $limit, 'after' => $cursor]);
    }

    public function fetchOrders($cursor = null, $limit = 10)
    {
        $query = <<<'GQL'
        query getOrders($first: Int!, $after: String) {
            orders(first: $first, after: $after, sortKey: PROCESSED_AT, reverse: true) {
                pageInfo {
                    hasNextPage
                    endCursor
                }
                edges {
                    node {
                        id
                        name
                        processedAt
                        totalPriceSet {
                            shopMoney {
                                amount
                                currencyCode
                            }
                        }
                        customer {
                            displayName
                            email
                        }
                    }
                }
            }
        }
GQL;

        return $this->execute($query, ['first' => $limit, 'after' => $cursor]);
    }

    protected function execute($query, $variables = [])
    {
        try {
            $response = $this->shop->api()->graph($query, $variables);

            if ($response['errors']) {
                Log::error('Shopify GraphQL Error', ['errors' => $response['errors'], 'shop' => $this->shop->name]);
                throw new Exception('Shopify GraphQL Error: ' . json_encode($response['errors']));
            }

            return $response['body']['data'];
        } catch (Exception $e) {
            Log::error('Shopify GraphQL Exception', ['message' => $e->getMessage(), 'shop' => $this->shop->name]);
            throw $e;
        }
    }
}
