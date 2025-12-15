<template>
    <div class="space-y-6">
        <!-- Header -->
        <h1 class="text-2xl font-bold">My Balances</h1>

        <!-- USD Balance -->
        <div class="p-6 bg-white shadow rounded-xl">
            <p class="text-sm text-gray-500">USD Balance</p>
            <p class="text-3xl font-semibold">
                {{ formatter.format(usdBalance) }}
            </p>
        </div>

        <!-- Asset Balances -->
        <div class="p-6 bg-white shadow rounded-xl">
            <h2 class="mb-4 text-lg font-semibold">Assets</h2>

            <div v-if="loading" class="text-gray-500">Loading balances...</div>

            <div v-else-if="assets.length === 0" class="text-gray-500">
                No assets available
            </div>

            <table v-else class="w-full text-left">
                <thead>
                    <tr class="text-sm text-gray-500 border-b">
                        <th class="pb-2">Asset</th>
                        <th class="pb-2 text-right">Balance</th>
                        <th class="pb-2 text-right">Locked Balance</th>
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
                            {{ asset.amount }}
                        </td>
                        <td class="py-3 text-right">
                            {{ asset.locked_amount }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { User } from "@/api";
import Echo from "@/echo";
import { useToast } from "vue-toastification";

const toast = useToast();
const auth = useAuthStore();
const usdBalance = ref("0.00");
const assets = ref([]);
const loading = ref(true);
let channel = null;

onMounted(async () => {
    fetchProfile();

    channel = Echo.private(`user.${auth.user.id}`).listen(
        ".order.matched",
        (event) => {
            let updatedInfo = event.data[auth.user.id];

            if (updatedInfo) {
                usdBalance.value = updatedInfo.balance;
                assets.value = updatedInfo.assets;
            }
        }
    );
});

onBeforeUnmount(() => {
    if (channel) Echo.leave(channel.name);
});

const fetchProfile = () => {
    loading.value = false;

    User.getProfile()
        .then((response) => {
            usdBalance.value = response.data.balance;
            assets.value = response.data.assets;
        })
        .catch(() => {
            toast.error("Error fetching profile.");
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>
