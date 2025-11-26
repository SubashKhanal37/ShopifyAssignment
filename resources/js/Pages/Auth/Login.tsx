import React, { useState } from "react";
import {
    Page,
    Layout,
    Card,
    FormLayout,
    TextField,
    Button,
    Text,
    BlockStack,
} from "@shopify/polaris";
import { AppProvider } from "@shopify/polaris";
import enTranslations from "@shopify/polaris/locales/en.json";
import "@shopify/polaris/build/esm/styles.css";

const Login = () => {
    const [shop, setShop] = useState("");

    const handleSubmit = () => {
        if (shop) {
            const url = `/authenticate?shop=${shop}`;
            if (window.top) {
                window.top.location.href = url;
            } else {
                window.location.href = url;
            }
        }
    };

    return (
        <AppProvider i18n={enTranslations}>
            <div
                style={{
                    height: "100vh",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    backgroundColor: "#f6f6f7",
                }}
            >
                <div style={{ width: "400px" }}>
                    <Page>
                        <BlockStack gap="500">
                            <Text
                                variant="headingXl"
                                as="h1"
                                alignment="center"
                            >
                                App Login
                            </Text>
                            <Card>
                                <FormLayout>
                                    <TextField
                                        label="Shop Domain"
                                        value={shop}
                                        onChange={setShop}
                                        placeholder="example.myshopify.com"
                                        autoComplete="off"
                                        helpText="Enter your Shopify store domain to install or login."
                                    />
                                    <Button
                                        variant="primary"
                                        onClick={handleSubmit}
                                        fullWidth
                                    >
                                        Login / Install
                                    </Button>
                                </FormLayout>
                            </Card>
                        </BlockStack>
                    </Page>
                </div>
            </div>
        </AppProvider>
    );
};

export default Login;
