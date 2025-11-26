import "./bootstrap";
import "../css/app.css";

import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { AppProvider as PolarisAppProvider } from "@shopify/polaris";
import { Provider as AppBridgeProvider } from "@shopify/app-bridge-react";
import "@shopify/polaris/build/esm/styles.css";
import enTranslations from "@shopify/polaris/locales/en.json";
import { PolarisLink } from "./Components/PolarisLink";
import axios from "axios";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

if (typeof window !== "undefined") {
    axios.interceptors.request.use(async (config) => {
        const w = window as any;
        if (w.shopify && w.shopify.id) {
            try {
                const token = await w.shopify.id.getToken();
                config.headers.Authorization = `Bearer ${token}`;
            } catch (error) {
            }
        }
        return config;
    });
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob("./Pages/**/*.tsx")
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        const urlParams = new URLSearchParams(window.location.search);
        const host = urlParams.get("host");
        const apiKey = document
            .querySelector('meta[name="shopify-api-key"]')
            ?.getAttribute("content");

        if (host && apiKey) {
            const config = {
                apiKey: apiKey,
                host: host,
                forceRedirect: false,
            };

            root.render(
                <AppBridgeProvider config={config}>
                    <PolarisAppProvider
                        i18n={enTranslations}
                        linkComponent={PolarisLink}
                    >
                        <App {...props} />
                    </PolarisAppProvider>
                </AppBridgeProvider>
            );
        } else {
            root.render(
                <PolarisAppProvider
                    i18n={enTranslations}
                    linkComponent={PolarisLink}
                >
                    <App {...props} />
                </PolarisAppProvider>
            );
        }
    },
    progress: {
        color: "#4B5563",
    },
});
