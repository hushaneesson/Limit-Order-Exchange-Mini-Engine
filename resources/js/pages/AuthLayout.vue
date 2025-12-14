<template>
    <div class="flex h-screen bg-gray-100">
        <!-- sidebar -->
        <div
            class="flex flex-col bg-gray-800 md:w-64"
            :class="{
                'w-full': openSideBar == true,
                'hidden md:flex': openSideBar == false,
            }"
        >
            <div
                class="flex items-center justify-between h-16 px-2 bg-teal-900 md:justify-center"
            >
                <span class="font-bold text-white uppercase">{{
                    auth.user.name
                }}</span>

                <button @click="openSideBar = false">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="text-gray-500 bg-white border border-gray-200 rounded-md size-8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18 18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
            <div class="flex flex-col flex-1 overflow-y-auto">
                <nav
                    @click="openSideBar = false"
                    class="flex-1 px-2 py-4 space-y-3 bg-teal-800"
                >
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
        <div
            class="flex flex-col flex-1 p-4 overflow-y-auto"
            :class="{
                'hidden md:block': openSideBar == true,
            }"
        >
            <div class="flex justify-end py-2">
                <button @click="openSideBar = true">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1"
                        stroke="currentColor"
                        class="text-gray-500 bg-white border border-gray-200 rounded-md size-8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                        />
                    </svg>
                </button>
            </div>
            <router-view />
        </div>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
const openSideBar = ref(false);
</script>
