<template>
    <div>
        <div class="flex flex-col justify-between gap-5 mb-12 sm:items-center">
            <h1 class="text-2xl font-bold">My Orders</h1>
            <router-link
                class="w-full btn btn-primary md:w-auto"
                :to="{ name: 'orders.create' }"
                >New Order</router-link
            >
        </div>

        <div v-if="loading" class="text-gray-500">Loading orders...</div>

        <div class="w-full overflow-x-auto bg-white shadow rounded-xl">
            <table
                v-if="!loading && orders.length"

            >
                <thead>
                    <tr class="text-left text-gray-600 border-b">
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Symbol</th>
                        <th class="px-4 py-2">Side</th>
                        <th class="px-4 py-2 text-right">Price</th>
                        <th class="px-4 py-2 text-right">Amount</th>
                        <th class="px-4 py-2 text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="order in orders"
                        :key="order.id"
                        class="border-b last:border-0"
                    >
                        <td class="px-4 py-2 whitespace-nowrap">
                            {{ moment(order.created_at).format("lll") }}
                        </td>
                        <td class="px-4 py-2 font-medium">
                            {{ order.symbol }}
                        </td>
                        <td
                            class="px-4 py-2"
                            :class="{
                                'text-green-600': order.side == 'buy',
                                'text-amber-600': order.side == 'sell',
                            }"
                        >
                            {{ order.side.toUpperCase() }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            ${{ order.price.toLocaleString() }}
                        </td>
                        <td class="px-4 py-2 text-right">{{ order.amount }}</td>
                        <td class="px-4 py-2 text-right">
                            <span
                                class="px-2 py-1 rounded-full whitespace-nowrap"
                                :class="{
                                    'text-sky-600 bg-blue-100':
                                        order.status == 1,
                                    'text-green-700 bg-green-100':
                                        order.status == 2,
                                    'text-amber-600 bg-amber-100':
                                        order.status == 1,
                                }"
                                >{{ statusMap[order.status] }}</span
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p v-if="!loading && orders.length === 0" class="text-gray-500">
            You have no orders.
        </p>
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { Order } from "@/api";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();

const symbol = ref(null);
const orders = ref([]);
const loading = ref(true);

const statusMap = {
    1: "OPEN",
    2: "FILLED",
    3: "CANCELLED",
};

onMounted(async () => {
    loading.value = false;

    Order.getAll({ symbol: symbol.value })
        .then((response) => {
            orders.value = response.data.orders;
        })
        .catch(() => {
            alert("Error fetching orders.");
        })
        .finally(() => {
            loading.value = false;
        });
});
</script>
