declare module "@shopify/app-bridge-react" {
  import * as React from "react";

  interface ProviderProps {
    config: any;
    children?: React.ReactNode;
  }

  export const Provider: React.ComponentType<ProviderProps>;
}


