<template>
    <transition
        enter-active-class="duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="duration-300 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="relative z-50"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
            ></div>

            <div
                @click="close"
                class="fixed inset-0 z-50 w-screen overflow-y-auto"
            >
                <div
                    class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0"
                >
                    <div
                        @click.stop
                        class="relative px-4 pt-5 pb-4 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                    >
                        <div class="sm:flex sm:items-start">
                            <div
                                class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full shrink-0 sm:mx-0 sm:h-10 sm:w-10"
                            >
                                <svg
                                    class="text-red-600 h-7 w-7"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                                    />
                                </svg>
                            </div>

                            <div
                                class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left"
                            >
                                <h3
                                    class="text-base font-semibold leading-6 text-gray-900"
                                    id="modal-title"
                                >
                                    <slot name="heading" />
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-700">
                                        <slot name="description" />
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button
                                @click="$emit('continue')"
                                type="button"
                                :disabled="processing"
                                class="w-full text-white bg-red-600 btn sm:ml-3 sm:w-auto"
                            >
                                {{ processing ? "Processing.." : "Continue" }}
                            </button>

                            <button
                                @click="close"
                                type="button"
                                class="w-full mt-3 btn btn-outline sm:mt-0 sm:w-auto"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";

// Props
defineProps({
    processing: {
        type: Boolean,
        default: false,
    },
});

// Emits
const emit = defineEmits(["cancel", "continue"]);

// State
const show = ref(false);

// Lifecycle
onMounted(async () => {
    nextTick(() => {
        show.value = true;
    });
});

// Methods
const close = async () => {
    show.value = false;

    nextTick(() => {
        emit("cancel");
    });
};
</script>
