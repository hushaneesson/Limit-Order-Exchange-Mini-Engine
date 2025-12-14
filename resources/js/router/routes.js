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
        ],
    },
];
