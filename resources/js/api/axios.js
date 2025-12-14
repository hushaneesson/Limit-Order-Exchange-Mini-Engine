import axios from "axios";
import router from "@/router/router";

// Create a new Axios instance with custom config
const api = axios.create({
    baseURL: "/api", // Base URL for all API requests
    timeout: 60000, // 60 second timeout
    withCredentials: true, // Required for cookies
});

// Add default headers
api.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
api.defaults.headers.common["Accept"] = "application/json";
api.defaults.headers.get["Content-Type"] = "application/json";
api.defaults.headers.post["Content-Type"] = "application/json";
api.defaults.headers.put["Content-Type"] = "application/json";

// Request interceptor
api.interceptors.request.use(
    async (config) => {
        // Automatically call CSRF endpoint for state-changing requests
        if (
            ["post", "put", "patch", "delete"].includes(
                config.method?.toLowerCase()
            )
        ) {
            await axios.get("/sanctum/csrf-cookie", { withCredentials: true });
        }

        return config;
    },
    (error) => Promise.reject(error)
);

// Response interceptor - handles 401/419 auth errors
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (
            error.response &&
            (error.response.status === 401 || error.response.status === 419)
        ) {
            const { useAuthStore } = await import("@/stores/auth");
            const authStore = useAuthStore();
            authStore.reset(); // Reset Pinia auth store

            router.push("/login"); // Redirect to login
            return Promise.resolve(); // Prevent further error propagation
        }

        return Promise.reject(error);
    }
);

export default api;
