<template>
    <div class="space-y-6 p-4">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold">My Balances</h1>
            <p class="text-gray-500">
                {{ auth.user?.name }}
            </p>
        </div>

        <!-- USD Balance -->
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">USD Balance</p>
            <p class="text-3xl font-semibold">
                {{ formatter.format(usdBalance) }}
            </p>
        </div>

        <!-- Asset Balances -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Assets</h2>

            <div v-if="loading" class="text-gray-500">Loading balances...</div>

            <div v-else-if="assets.length === 0" class="text-gray-500">
                No assets available
            </div>

            <table v-else class="w-full text-left">
                <thead>
                    <tr class="border-b text-sm text-gray-500">
                        <th class="pb-2">Asset</th>
                        <th class="pb-2 text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="asset in assets"
                        :key="asset.symbol"
                        class="border-b last:border-0"
                    >
                        <td class="py-3 font-medium">
                            {{ asset.symbol }}
                        </td>
                        <td class="py-3 text-right">
                            {{ asset.balance }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { User } from "@/api";

const auth = useAuthStore();

const usdBalance = ref("0.00");
const assets = ref([]);
const loading = ref(true);

onMounted(async () => {
    loading.value = false;

    User.getProfile()
        .then((response) => {
            usdBalance.value = response.data.balance;
            assets.value = response.data.assets;
        })
        .catch(() => {
            alert("Error fetching profile.");
        })
        .finally(() => {
            loading.value = false;
        });
});
</script>
