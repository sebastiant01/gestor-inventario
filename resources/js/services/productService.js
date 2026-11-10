import { apiClient } from './apiClient.js';

export const productService = {
    async getAll() {
        return await apiClient.get('/products');
    },

    async getById(id) {
        return await apiClient.get(`/products/${id}`);
    },

    async create(productData) {
        return await apiClient.post('/products', productData);
    },

    async update(id, productData) {
        return await apiClient.put(`/products/${id}`, productData);
    },

    async delete(id) {
        return await apiClient.delete(`/products/${id}`);
    }
};