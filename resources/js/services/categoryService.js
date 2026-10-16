import { apiClient } from './apiClient.js';

export const categoryService = {
    async getAll() {
        return await apiClient.get('/categories');
    },

    async getById(id) {
        return await apiClient.get(`/categories/${id}`);
    },

    async create(categoryData) {
        return await apiClient.post('/categories', categoryData);
    },

    async update(id, categoryData) {
        return await apiClient.put(`/categories/${id}`, categoryData);
    },

    async delete(id) {
        return await apiClient.delete(`/categories/${id}`);
    }
};