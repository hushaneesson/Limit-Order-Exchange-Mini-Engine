<template>
    <div>
        <div
            class="flex flex-col justify-between gap-5 mb-12 sm:flex-row sm:items-center"
        >
            <h1 class="text-2xl font-bold">My Orders</h1>
            <router-link
                class="w-full btn btn-primary md:w-auto"
                :to="{ name: 'orders.create' }"
                >New Order</router-link
            >
        </div>

        <div v-if="loading" class="text-gray-500">Loading orders...</div>

        <p v-if="!loading && orders.length == 0" class="text-gray-500">
            You have no orders.
        </p>

        <div class="mb-6">
            <div
                class="p-4 bg-white border border-gray-200 shadow-lg rounded-xl"
            >
                <h2 class="font-bold text-stone-700">Filters</h2>

                <div
                    class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <div class="flex flex-col">
                        <label
                            for="symbol"
                            class="text-sm font-medium text-stone-600"
                            >Symbol</label
                        >

                        <select
                            id="symbol"
                            v-model="filters.symbol"
                            class="block w-full px-2 py-2 mt-2 border border-gray-200 rounded-md shadow-sm outline-none focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                        >
                            <option value="">Choose One</option>
                            <option>BTC</option>
                            <option>ETH</option>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label
                            for="side"
                            class="text-sm font-medium text-stone-600"
                            >Side</label
                        >

                        <select
                            id="side"
                            v-model="filters.side"
                            class="block w-full px-2 py-2 mt-2 border border-gray-200 rounded-md shadow-sm outline-none focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                        >
                            <option value="">Choose One</option>
                            <option value="buy">Buy</option>
                            <option value="sell">Sell</option>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label
                            for="status"
                            class="text-sm font-medium text-stone-600"
                            >Status</label
                        >

                        <select
                            id="status"
                            v-model="filters.status"
                            class="block w-full px-2 py-2 mt-2 border border-gray-200 rounded-md shadow-sm outline-none focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                        >
                            <option value="">Choose One</option>
                            <option value="1">Open</option>
                            <option value="2">Filled</option>
                            <option value="3">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div
                    class="grid justify-end w-full grid-cols-2 mt-6 space-x-4 md:flex"
                >
                    <button @click="reset" class="btn btn-outline">
                        Reset
                    </button>
                    <button @click="fetchOrders" class="bg-gray-300 btn">
                        Search
                    </button>
                </div>
            </div>
        </div>

        <div class="w-full overflow-x-auto bg-white shadow rounded-xl">
            <table class="w-full" v-if="!loading && orders.length">
                <thead>
                    <tr class="text-left text-gray-600 border-b">
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Symbol</th>
                        <th class="px-4 py-2">Side</th>
                        <th class="px-4 py-2 text-right">Price</th>
                        <th class="px-4 py-2 text-right">Amount</th>
                        <th class="px-4 py-2 text-right">Status</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="order in orders"
                        :key="order.id"
                        class="border-b last:border-0"
                        :class="{ 'bg-teal-100': updatedOrder == order.id }"
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
                                class="px-2 py-1 text-sm rounded-full whitespace-nowrap"
                                :class="{
                                    'text-sky-600 bg-teal-100':
                                        order.status == 1,
                                    'text-green-700 bg-green-100':
                                        order.status == 2,
                                    'text-amber-600 bg-amber-100':
                                        order.status == 3,
                                }"
                                >{{ statusMap[order.status] }}</span
                            >
                        </td>
                        <td class="px-4 py-2 text-right">
                            <button
                                v-if="order.status == 1"
                                @click="selectedOrder = order"
                                class="text-red-600 btn"
                            >
                                Cancel
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <CancelOrder
            v-if="selectedOrder"
            :processing="cancellingOrder"
            @cancel="selectedOrder = null"
            @continue="cancelOrder"
        >
            <template #heading> Cancel Order </template>
            <template #description>
                Are you certain that you want to cancel this order?
            </template>
        </CancelOrder>
    </div>
</template>
<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Order } from "@/api";
import { useAuthStore } from "@/stores/auth";
import CancelOrder from "@/components/WarningModal.vue";
import Echo from "@/echo";
import { useToast } from "vue-toastification";

const toast = useToast();
const auth = useAuthStore();
const filters = ref({
    symbol: "",
    side: "",
    status: "",
});
const orders = ref([]);
const loading = ref(true);
const cancellingOrder = ref(false);
const selectedOrder = ref(null);
const updatedOrder = ref(null);
let channel = null;

const statusMap = {
    1: "OPEN",
    2: "FILLED",
    3: "CANCELLED",
};

onMounted(async () => {
    fetchOrders();

    channel = Echo.private(`user.${auth.user.id}`).listen(
        ".order.matched",
        (event) => {
            let updatedInfo = event.data[auth.user.id];

            if (updatedInfo) {
                toast.success("New matched order!");

                let order = orders.value.find(
                    (order) => order.id === updatedInfo.updated_order_id
                );

                if (order) {
                    order.status = 2;
                    updatedOrder.value = order.id;
                }
            }
        }
    );
});

onBeforeUnmount(() => {
    if (channel) Echo.leave(channel.name);
});

const reset = () => {
    filters.value = { symbol: "", side: "", status: "" };

    fetchOrders();
};

const fetchOrders = () => {
    loading.value = true;

    Order.getAll(filters.value)
        .then((response) => {
            orders.value = response.data.orders;
        })
        .catch(() => {
            toast.error("Error fetching orders.");
        })
        .finally(() => {
            loading.value = false;
        });
};

const cancelOrder = () => {
    cancellingOrder.value = true;

    Order.cancel(selectedOrder.value.id)
        .then((response) => {
            selectedOrder.value.status = 3;
            selectedOrder.value = null;

            toast.success("Order successfully cancelled.");
        })
        .catch((error) => {
            toast.error(error.response?.data || "Failed to cancel order");
        })
        .finally(() => {
            cancellingOrder.value = false;
        });
};
</script>
