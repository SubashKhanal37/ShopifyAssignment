import { Link as InertiaLink } from '@inertiajs/react';
import React from 'react';

export const PolarisLink = ({ children, url, external, ...rest }: any) => {
    if (external) {
        return (
            <a href={url} target="_blank" rel="noopener noreferrer" {...rest}>
                {children}
            </a>
        );
    }

    return (
        <InertiaLink href={url} {...rest}>
            {children}
        </InertiaLink>
    );
};

