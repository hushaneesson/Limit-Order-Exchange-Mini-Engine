<template>
    <div
        class="flex flex-col justify-center min-h-screen px-2 py-12 bg-white sm:px-6 lg:px-8"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="px-4 py-8 bg-white rounded-lg shadow-lg sm:px-10">
                <!-- Header -->
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-bold text-teal-600 sm:text-3xl">
                        Welcome Back
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 sm:text-base">
                        Please sign in to your account
                    </p>
                </div>

                <!-- Form -->
                <form class="space-y-6" @submit.prevent="login">
                    <p v-if="error" class="text-red-600">* {{ error }}</p>

                    <!-- Email -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700"
                            for="email"
                            >Email Address</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            id="email"
                            class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg sm:px-4 sm:py-3 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-teal-500"
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700"
                            for="password"
                            >Password</label
                        >
                        <div class="relative mt-1">
                            <input
                                v-model="form.password"
                                type="password"
                                id="password"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg sm:px-4 sm:py-3 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-teal-500"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full btn btn-primary"
                    >
                        {{ loading ? "Signing in..." : "Sign In" }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { User } from "@/api";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    email: "",
    password: "",
});

const loading = ref(false);
const error = ref(null);

const login = async () => {
    error.value = null;

    if (!form.email || !form.password) {
        error.value = "Please enter both email and password";
        return;
    }

    loading.value = true;

    User.login(form)
        .then((response) => {
            authStore.setLoginData(response.data.user);

            router.push({ name: "profile" });
        })
        .catch(() => {
            error.value = "Invalid email or password";
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>
