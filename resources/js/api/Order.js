import axios from "./axios";

class Order {
    /**
     * Get all orders
     * @param {Object} params - Optional filtering parameters
     * @returns {Promise} - Axios promise with orders data
     */
    static getAll(params = {}) {
        return axios.get("/orders", { params });
    }

    /**
     * Create new order
     * @param {Object} orderData - Order data
     * @returns {Promise} - Axios promise with creation result
     */
    static create(orderData) {
        return axios.post("/orders", orderData);
    }

    /**
     * Cancel open order
     * @param {Object} orderId - Order id
     * @returns {Promise} - Axios promise with cancellation result
     */
    static cancel(orderId) {
        return axios.post(`/orders/${orderId}/cancel`);
    }
}

export default Order;
