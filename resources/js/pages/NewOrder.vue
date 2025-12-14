<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="mb-12 text-lg font-semibold text-center text-gray-800">
            New Limit Order
        </h2>

        <!-- Error -->
        <div
            v-if="error"
            class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded"
        >
            {{ error }}
        </div>

        <form class="space-y-8" @submit.prevent="submit">
            <!-- order_type -->
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">
                    Order Type
                </label>
                <div class="flex gap-2">
                    <button
                        type="button"
                        @click="form.order_type = 'buy'"
                        :class="order_typeClass('buy')"
                    >
                        Buy
                    </button>
                    <button
                        type="button"
                        @click="form.order_type = 'sell'"
                        :class="order_typeClass('sell')"
                    >
                        Sell
                    </button>
                </div>
            </div>

            <!-- Symbol -->
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Symbol
                </label>
                <select
                    v-model="form.symbol"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500 focus:outline-none"
                >
                    <option value="">Please Select Symbol</option>
                    <option value="BTC">BTC</option>
                    <option value="ETH">ETH</option>
                </select>
            </div>

            <!-- Price -->
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Price (USD)
                </label>
                <input
                    v-model.number="form.price"
                    type="number"
                    step="0.01"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500 focus:outline-none"
                />
            </div>

            <!-- Amount -->
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">
                    Amount
                </label>
                <input
                    v-model.number="form.amount"
                    type="number"
                    step="0.0001"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500 focus:outline-none"
                />
            </div>

            <!-- Total -->
            <div class="mb-4 text-gray-600">
                Total:
                <strong class="text-gray-800">
                    {{ formatter.format(total) }}
                </strong>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="loading"
                class="w-full btn btn-primary"
            >
                {{ loading ? "Placing Order..." : "Place Order" }}
            </button>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { Order } from "@/api";

const router = useRouter();
const loading = ref(false);
const error = ref(null);

const form = reactive({
    symbol: "",
    order_type: "buy",
    price: null,
    amount: null,
});

const total = computed(() => {
    if (!form.price || !form.amount) return 0;
    return form.price * form.amount;
});

const order_typeClass = (order_type) => {
    const base = "flex-1 py-2 rounded text-sm font-medium border transition";

    if (form.order_type === order_type) {
        return order_type === "buy"
            ? `${base} bg-teal-600 text-white border-teal-600`
            : `${base} bg-teal-500 text-white border-teal-500`;
    }

    return `${base} bg-white text-gray-700 border-gray-300 hover:bg-gray-50`;
};

const submit = async () => {
    error.value = null;

    if (!form.symbol || !form.price || !form.amount) {
        error.value = "All fields are required";
        return;
    }

    loading.value = true;

    Order.create(form)
        .then((response) => {
            alert("Order submitted successfully");

            router.push({ name: "orders" });
        })
        .catch((error) => {
            alert(error.response.data);
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>
