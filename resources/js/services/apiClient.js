// Cliente HTTP base para peticiones
class ApiClient {
    constructor() {
        this.baseUrl = window.location.origin;
    }

    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    getAuthToken() {
        return localStorage.getItem('auth_token') || '';
    }

    async request(endpoint, method = 'GET', body = null) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest'
        };

        const token = this.getAuthToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = { method, headers };
        if (body) config.body = JSON.stringify(body);

        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, config);

            if (response.status === 401) {
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
                return;
            }

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || `Error HTTP ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error(`[API Error - ${method} ${endpoint}]:`, error.message);
            throw error;
        }
    }

    get(endpoint) { return this.request(endpoint, 'GET'); }
    post(endpoint, body) { return this.request(endpoint, 'POST', body); }
    put(endpoint, body) { return this.request(endpoint, 'PUT', body); }
    delete(endpoint) { return this.request(endpoint, 'DELETE'); }
}

export const apiClient = new ApiClient();