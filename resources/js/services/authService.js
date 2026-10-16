import { apiClient } from './apiClient.js';

export const authService = {
    async login(email, password) {
        const response = await apiClient.post('/login', { email, password });
        if (response.token) {
            localStorage.setItem('auth_token', response.token);
        }
        return response;
    },

    async register(userData) {
        return await apiClient.post('/register', userData);
    },

    async logout() {
        try {
            await apiClient.post('/logout');
        } finally {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
    },

    async getCurrentUser() {
        return await apiClient.get('/user');
    }
};