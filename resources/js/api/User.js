import axios from "./axios";

class User {
    /**
     * Get the current authenticated user profile
     * @returns {Promise} - Axios promise with user profile data
     */
    static getProfile() {
        return axios.get("/profile");
    }

    /**
     * Login a user
     * @param {Object} credentials - User credentials
     * @returns {Promise} - Axios promise with login result
     */
    static login(credentials) {
        return axios.get("/sanctum/csrf-cookie").then(() => {
            return axios.post("/login", credentials);
        });
    }

    /**
     * Logout the current user
     * @returns {Promise} - Axios promise with logout result
     */
    static logout() {
        return axios.post("/logout");
    }
}

export default User;
