export default [
    {
        name: "auth.login",
        path: "/login",
        component: () => import("@/pages/Login.vue"),
        meta: {
            title: "Login",
            requiresAuth: false,
        },
    },
    {
        path: "/",
        component: () => import("@/pages/AuthLayout.vue"),
        meta: {
            requiresAuth: true,
        },
        children: [
            {
                name: "profile",
                path: "",
                component: () => import("@/pages/Profile.vue"),
                meta: {
                    title: "Profile",
                },
            },
            {
                name: "orders",
                path: "orders",
                component: () => import("@/pages/orders.vue"),
                meta: {
                    title: "Orders",
                },
            },
            {
                name: "orders.create",
                path: "orders/create",
                component: () => import("@/pages/NewOrder.vue"),
                meta: {
                    title: "New Order",
                },
            },
        ],
    },
];
