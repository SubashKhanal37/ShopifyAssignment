import React, { useState } from "react";
import {
    Page,
    Layout,
    LegacyCard,
    Text,
    BlockStack,
    Button,
    InlineStack,
} from "@shopify/polaris";
import AppLayout from "../Components/AppLayout";
import { router } from "@inertiajs/react";

interface DashboardProps {
    stats: {
        productsCount: number;
        collectionsCount: number;
        ordersCount: number;
        lastSync: string | null;
    };
}

const Dashboard = ({ stats }: DashboardProps) => {
    const [productsSyncing, setProductsSyncing] = useState(false);
    const [collectionsSyncing, setCollectionsSyncing] = useState(false);
    const [ordersSyncing, setOrdersSyncing] = useState(false);
    const [toastMessage, setToastMessage] = useState<string | null>(null);

    const handleSyncProducts = () => {
        setProductsSyncing(true);
        router.post(
            "/products/sync",
            {},
            {
                onSuccess: () => {
                    setToastMessage("Products synced");
                },
                onFinish: () => {
                    setProductsSyncing(false);
                },
            }
        );
    };

    const handleSyncCollections = () => {
        setCollectionsSyncing(true);
        router.post(
            "/collections/sync",
            {},
            {
                onSuccess: () => {
                    setToastMessage("Collections synced");
                },
                onFinish: () => {
                    setCollectionsSyncing(false);
                },
            }
        );
    };

    const handleSyncOrders = () => {
        setOrdersSyncing(true);
        router.post(
            "/orders/sync",
            {},
            {
                onSuccess: () => {
                    setToastMessage("Orders synced");
                },
                onFinish: () => {
                    setOrdersSyncing(false);
                },
            }
        );
    };

    return (
        <AppLayout>
            <Page title="Dashboard">
                <Layout>
                    <Layout.Section>
                        <LegacyCard title="Overview" sectioned>
                            <BlockStack gap="400">
                                <Text as="p" variant="bodyMd">
                                    Welcome to your Shopify App Dashboard.
                                </Text>
                                <InlineStack gap="400">
                                    <Button
                                        onClick={handleSyncProducts}
                                        variant="primary"
                                        loading={productsSyncing}
                                        disabled={
                                            productsSyncing ||
                                            collectionsSyncing ||
                                            ordersSyncing
                                        }
                                    >
                                        Sync Products
                                    </Button>
                                    <Button
                                        onClick={handleSyncCollections}
                                        loading={collectionsSyncing}
                                        disabled={
                                            productsSyncing ||
                                            collectionsSyncing ||
                                            ordersSyncing
                                        }
                                    >
                                        Sync Collections
                                    </Button>
                                    <Button
                                        onClick={handleSyncOrders}
                                        loading={ordersSyncing}
                                        disabled={
                                            productsSyncing ||
                                            collectionsSyncing ||
                                            ordersSyncing
                                        }
                                    >
                                        Sync Orders
                                    </Button>
                                </InlineStack>
                                {stats.lastSync && (
                                    <Text as="p" tone="subdued">
                                        Last synced:{" "}
                                        {new Date(
                                            stats.lastSync
                                        ).toLocaleString()}
                                    </Text>
                                )}
                            </BlockStack>
                        </LegacyCard>
                    </Layout.Section>

                    <Layout.Section variant="oneHalf">
                        <LegacyCard title="Total Products" sectioned>
                            <Text as="h2" variant="heading3xl">
                                {stats.productsCount}
                            </Text>
                        </LegacyCard>
                    </Layout.Section>

                    <Layout.Section variant="oneHalf">
                        <LegacyCard title="Total Collections" sectioned>
                            <Text as="h2" variant="heading3xl">
                                {stats.collectionsCount}
                            </Text>
                        </LegacyCard>
                    </Layout.Section>

                    <Layout.Section variant="oneHalf">
                        <LegacyCard title="Total Orders" sectioned>
                            <Text as="h2" variant="heading3xl">
                                {stats.ordersCount}
                            </Text>
                        </LegacyCard>
                    </Layout.Section>
                </Layout>
            </Page>
        </AppLayout>
    );
};

export default Dashboard;
