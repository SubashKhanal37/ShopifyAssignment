import React, { useState, useCallback } from 'react';
import { Frame, Navigation, TopBar } from '@shopify/polaris';
import { HomeIcon, ProductIcon, CollectionIcon, ExitIcon } from '@shopify/polaris-icons';
import { usePage } from '@inertiajs/react';
import { useLogout } from './LogoutButton';
import type { UserMenuProps } from '@shopify/polaris/build/ts/src/components/TopBar';

export default function AppLayout({ children }: { children: React.ReactNode }) {
    const { url } = usePage();
    const [mobileNavigationActive, setMobileNavigationActive] = useState(false);
    const [userMenuActive, setUserMenuActive] = useState(false);
    const logout = useLogout();

    const toggleMobileNavigationActive = useCallback(
        () => setMobileNavigationActive((mobileNavigationActive) => !mobileNavigationActive),
        [],
    );

    const toggleUserMenuActive = useCallback(
        () => setUserMenuActive((userMenuActive) => !userMenuActive),
        [],
    );

    const navigationMarkup = (
        <Navigation location={url}>
            <Navigation.Section
                items={[
                    {
                        url: '/dashboard',
                        label: 'Dashboard',
                        icon: HomeIcon,
                        selected: url.startsWith('/dashboard') || url === '/',
                    },
                    {
                        url: '/products',
                        label: 'Products',
                        icon: ProductIcon,
                        selected: url.startsWith('/products'),
                    },
                    {
                        url: '/collections', // Optional if we implement a page, but useful for sync button context if we had one
                        label: 'Collections',
                        icon: CollectionIcon,
                        selected: url.startsWith('/collections'),
                        disabled: true // Since we don't have a page yet, or enable if we add it
                    },
                ]}
            />
        </Navigation>
    );

    const userMenuActions: UserMenuProps['actions'] = [
        {
            items: [
                {
                    content: 'Logout',
                    icon: ExitIcon,
                    onAction: logout,
                },
            ],
        },
    ];

    const userMenuMarkup = (
        <TopBar.UserMenu
            actions={userMenuActions}
            name="User"
            initials="U"
            open={userMenuActive}
            onToggle={toggleUserMenuActive}
        />
    );

    const topBarMarkup = (
        <TopBar
            showNavigationToggle
            onNavigationToggle={toggleMobileNavigationActive}
            userMenu={userMenuMarkup}
        />
    );

    return (
        <Frame
            topBar={topBarMarkup}
            navigation={navigationMarkup}
            showMobileNavigation={mobileNavigationActive}
            onNavigationDismiss={toggleMobileNavigationActive}
        >
            {children}
        </Frame>
    );
}

