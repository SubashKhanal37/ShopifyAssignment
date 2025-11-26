import React, { useState, useCallback } from "react";
import {
    Page,
    Layout,
    LegacyCard,
    IndexTable,
    TextField,
    Select,
    Filters,
    useIndexResourceState,
    Text,
    Badge,
    Pagination,
    Spinner,
} from "@shopify/polaris";
import AppLayout from "../Components/AppLayout";
import { router } from "@inertiajs/react";

interface Product {
    id: number;
    title: string;
    status: string;
    product_type: string;
    vendor: string;
    shopify_id: string;
    images: any;
}

interface ProductsProps {
    products: {
        data: Product[];
        current_page: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        total: number;
        from: number;
        to: number;
    };
    filters: {
        search?: string;
        status?: string;
    };
}

const Products = ({ products, filters }: ProductsProps) => {
    const [queryValue, setQueryValue] = useState(filters.search || "");
    const [status, setStatus] = useState(filters.status || "");
    const [paginating, setPaginating] = useState(false);

    const handleQueryChange = useCallback((value: string) => {
        setQueryValue(value);
        // Debounce could be added here
    }, []);

    const handleQueryClear = useCallback(() => setQueryValue(""), []);
    const handleStatusChange = useCallback(
        (value: string) => setStatus(value),
        []
    );
    const handleStatusRemove = useCallback(() => setStatus(""), []);

    const applyFilters = useCallback(() => {
        router.get(
            "/products",
            { search: queryValue, status },
            { preserveState: true }
        );
    }, [queryValue, status]);

    const handleKeyPress = (event: React.KeyboardEvent) => {
        if (event.key === "Enter") {
            applyFilters();
        }
    };

    const resourceName = {
        singular: "product",
        plural: "products",
    };

    const { selectedResources, allResourcesSelected, handleSelectionChange } =
        useIndexResourceState(products.data as any);

    const rowMarkup = products.data.map(
        ({ id, title, status, vendor, product_type, images }, index) => (
            <IndexTable.Row
                id={id.toString()}
                key={id}
                selected={selectedResources.includes(id.toString())}
                position={index}
            >
                <IndexTable.Cell>
                    <Text variant="bodyMd" fontWeight="bold" as="span">
                        {title}
                    </Text>
                </IndexTable.Cell>
                <IndexTable.Cell>
                    {status && <Badge>{status}</Badge>}
                </IndexTable.Cell>
                <IndexTable.Cell>{product_type}</IndexTable.Cell>
                <IndexTable.Cell>{vendor}</IndexTable.Cell>
            </IndexTable.Row>
        )
    );

    const goToPage = (url: string | null) => {
        if (!url) return;

        setPaginating(true);

        try {
            const parsed = new URL(url);
            const page =
                parsed.searchParams.get("page") || products.current_page;

            router.get(
                "/products",
                {
                    page,
                    search: queryValue,
                    status,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    onFinish: () => setPaginating(false),
                }
            );
        } catch {
            router.get(
                "/products",
                {
                    search: queryValue,
                    status,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    onFinish: () => setPaginating(false),
                }
            );
        }
    };

    const handleNext = () => {
        goToPage(products.next_page_url);
    };

    const handlePrev = () => {
        goToPage(products.prev_page_url);
    };

    const filtersMarkup = [
        {
            key: "status",
            label: "Status",
            filter: (
                <Select
                    label="Status"
                    labelHidden
                    options={[
                        { label: "All", value: "" },
                        { label: "Active", value: "ACTIVE" },
                        { label: "Draft", value: "DRAFT" },
                        { label: "Archived", value: "ARCHIVED" },
                    ]}
                    value={status}
                    onChange={(val) => {
                        setStatus(val);
                        router.get(
                            "/products",
                            { search: queryValue, status: val },
                            { preserveState: true }
                        );
                    }}
                />
            ),
            shortcut: true,
        },
    ];

    return (
        <AppLayout>
            <Page title="Products">
                <Layout>
                    <Layout.Section>
                        <LegacyCard>
                            <div style={{ position: "relative" }}>
                                <div
                                    style={{
                                        padding: "16px",
                                        display: "flex",
                                        gap: "10px",
                                        opacity: paginating ? 0.5 : 1,
                                    }}
                                >
                                    <div style={{ flex: 1 }}>
                                        <TextField
                                            label="Search products"
                                            labelHidden
                                            value={queryValue}
                                            onChange={handleQueryChange}
                                            onClearButtonClick={() => {
                                                handleQueryClear();
                                                router.get(
                                                    "/products",
                                                    { search: "", status },
                                                    { preserveState: true }
                                                );
                                            }}
                                            autoComplete="off"
                                            placeholder="Search by title"
                                            prefix={
                                                <Text as="span" tone="subdued">
                                                    🔍
                                                </Text>
                                            }
                                            connectedRight={
                                                <button
                                                    onClick={applyFilters}
                                                    style={{
                                                        padding: "0 10px",
                                                        cursor: "pointer",
                                                    }}
                                                >
                                                    Search
                                                </button>
                                            }
                                            onBlur={applyFilters}
                                        />
                                    </div>
                                    <Select
                                        label="Status"
                                        labelHidden
                                        options={[
                                            { label: "Status: All", value: "" },
                                            { label: "Active", value: "ACTIVE" },
                                            { label: "Draft", value: "DRAFT" },
                                            {
                                                label: "Archived",
                                                value: "ARCHIVED",
                                            },
                                        ]}
                                        value={status}
                                        onChange={(val) => {
                                            setStatus(val);
                                            router.get(
                                                "/products",
                                                {
                                                    search: queryValue,
                                                    status: val,
                                                },
                                                { preserveState: true }
                                            );
                                        }}
                                    />
                                </div>

                                <IndexTable
                                    resourceName={resourceName}
                                    itemCount={products.total}
                                    selectedItemsCount={
                                        allResourcesSelected
                                            ? "All"
                                            : selectedResources.length
                                    }
                                    onSelectionChange={handleSelectionChange}
                                    headings={[
                                        { title: "Title" },
                                        { title: "Status" },
                                        { title: "Type" },
                                        { title: "Vendor" },
                                    ]}
                                >
                                    {rowMarkup}
                                </IndexTable>

                                <div
                                    style={{
                                        display: "flex",
                                        justifyContent: "center",
                                        padding: "20px",
                                    }}
                                >
                                    <Pagination
                                        hasPrevious={!!products.prev_page_url}
                                        onPrevious={
                                            paginating ? undefined : handlePrev
                                        }
                                        hasNext={!!products.next_page_url}
                                        onNext={
                                            paginating ? undefined : handleNext
                                        }
                                        label={`${products.from || 0}-${
                                            products.to || 0
                                        } of ${products.total}`}
                                    />
                                </div>

                                {paginating && (
                                    <div
                                        style={{
                                            position: "absolute",
                                            inset: 0,
                                            display: "flex",
                                            alignItems: "center",
                                            justifyContent: "center",
                                        }}
                                    >
                                        <Spinner accessibilityLabel="Loading products" />
                                    </div>
                                )}
                            </div>
                        </LegacyCard>
                    </Layout.Section>
                </Layout>
            </Page>
        </AppLayout>
    );
};

export default Products;
