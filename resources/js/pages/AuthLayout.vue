<template>
    <div class="flex h-screen bg-gray-100">
        <!-- sidebar -->
        <div class="hidden md:flex flex-col w-64 bg-gray-800">
            <div class="flex items-center justify-center h-16 bg-teal-900">
                <span class="text-white font-bold uppercase">Pages</span>
            </div>
            <div class="flex flex-col flex-1 overflow-y-auto">
                <nav class="flex-1 px-2 py-4 bg-teal-800">
                    <router-link
                        :to="{ name: 'profile' }"
                        class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700"
                    >
                        Profile
                    </router-link>
                    <router-link
                        :to="{ name: 'profile' }"
                        class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700"
                    >
                        Orders
                    </router-link>
                    <button
                        type="button"
                        @click="auth.logout()"
                        class="flex w-full items-center px-4 py-2 text-gray-100 hover:bg-gray-700"
                    >
                        Logout
                    </button>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-y-auto">
            <router-view />
        </div>
    </div>
</template>
<script setup>
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
</script>
