<template>
    <div
        class="min-h-screen bg-white flex flex-col justify-center py-12 sm:px-6 lg:px-8"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-teal-600">
                        Welcome Back
                    </h2>
                    <p class="mt-2 text-sm sm:text-base text-gray-600">
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
                            class="mt-1 block w-full px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-teal-500"
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700"
                            for="password"
                            >Password</label
                        >
                        <div class="mt-1 relative">
                            <input
                                v-model="form.password"
                                type="password"
                                id="password"
                                class="block w-full px-3 py-2 sm:px-4 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:outline-teal-500"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full flex justify-center py-2 px-4 sm:py-3 border border-transparent rounded-lg shadow-sm text-sm sm:text-base font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                    >
                        <span>Sign In</span>
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
