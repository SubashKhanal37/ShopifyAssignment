import { useCallback } from 'react';
import axios from 'axios';

export function useLogout() {
    const logout = useCallback(async () => {
        try {
            const response = await axios.post('/logout');
            
            if (response.data.success) {
                if (window.top && window.top !== window.self) {
                    window.top.location.href = response.data.redirect;
                } else {
                    window.location.href = response.data.redirect;
                }
            }
        } catch (error) {
            console.error('Logout failed:', error);
            window.location.href = '/login';
        }
    }, []);

    return logout;
}

