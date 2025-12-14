import { createApp } from "vue";
import { createPinia } from "pinia";
import moment from "moment";

import App from "./App.vue";
import router from "./router/router";

// Create Pinia store
const pinia = createPinia();

// Create Vue app
const app = createApp(App);

// Use Pinia state management
app.use(pinia);

// Use router
app.use(router);

app.config.globalProperties.moment = moment;
app.config.globalProperties.formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
});

// Mount app
app.mount("#app");
