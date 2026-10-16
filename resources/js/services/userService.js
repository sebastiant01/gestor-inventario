import { apiClient } from './apiClient.js';

export const userService = {
    async getAll() {
        return await apiClient.get('/users');
    },

    async getById(id) {
        return await apiClient.get(`/users/${id}`);
    },

    async create(userData) {
        return await apiClient.post('/users', userData);
    },

    async update(id, userData) {
        return await apiClient.put(`/users/${id}`, userData);
    },

    async delete(id) {
        return await apiClient.delete(`/users/${id}`);
    }
};