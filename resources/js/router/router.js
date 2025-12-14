import { createWebHistory, createRouter } from "vue-router";
import routes from "./routes";
import { useAuthStore } from "@/stores/auth";

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

// Add a global navigation guard
router.beforeEach((to, from, next) => {
    // Set document title
    document.title = to.meta.title;

    // Get auth state from store
    const store = useAuthStore();
    const isAuthenticated = store.isAuthenticated;

    // Handle authentication requirements
    if (!to.meta.requiresAuth && isAuthenticated) {
        next({ name: "profile" });
        return;
    }

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: "auth.login" });
        return;
    }

    next();
});

export default router;
