import { defineStore } from "pinia";
import router from "@/router/router";
import { User } from "@/api";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.user,
    },

    actions: {
        init() {
            this.user = JSON.parse(localStorage.getItem("user")) || null;
        },

        setLoginData(user) {
            this.user = user;
            localStorage.setItem("user", JSON.stringify(user));
        },

        logout() {
            User.logout().finally(() => {
                this.reset();
                router.push({ name: "auth.login" });
            });
        },

        // Clear auth state
        reset() {
            this.user = null;
            localStorage.clear();
        },
    },
});
