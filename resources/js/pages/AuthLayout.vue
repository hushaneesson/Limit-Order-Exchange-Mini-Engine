<template>
    <div class="flex h-screen bg-gray-100">
        <!-- sidebar -->
        <div class="flex-col hidden w-64 bg-gray-800 md:flex">
            <div class="flex items-center justify-center h-16 bg-teal-900">
                <span class="font-bold text-white uppercase">{{
                    auth.user.name
                }}</span>
            </div>
            <div class="flex flex-col flex-1 overflow-y-auto">
                <nav class="flex-1 px-2 py-4 space-y-3 bg-teal-800">
                    <router-link
                        :to="{ name: 'profile' }"
                        class="nav-link"
                        :class="{ 'bg-teal-900': $route.name == 'profile' }"
                    >
                        Profile
                    </router-link>
                    <router-link
                        :to="{ name: 'orders' }"
                        class="nav-link"
                        :class="{
                            'bg-teal-900': $route.name.includes('orders'),
                        }"
                    >
                        Orders
                    </router-link>
                    <button
                        type="button"
                        @click="auth.logout()"
                        class="w-full nav-link"
                    >
                        Logout
                    </button>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col flex-1 p-4 overflow-y-auto">
            <router-view />
        </div>
    </div>
</template>
<script setup>
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
</script>
