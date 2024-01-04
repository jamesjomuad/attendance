import store from '../store'

const routes = [
    // Time In/Out
    {
        path: "/",
        component: () => import("../layouts/BlankLayout.vue"),
        children: [
            {
                name: "Time In/Out",
                path: "",
                component: () => import("../pages/Auth/TimePage.vue"),
                beforeEnter: (to, from) => {
                    if (store.getters['auth/isAuthenticated']) {
                        return false;
                    }
                }
            }
        ]
    },

    // BlankLayout
    {
        path: "/auth",
        component: () => import("../layouts/BlankLayout.vue"),
        children: [{
                name: "Login",
                path: "/login",
                component: () => import("../pages/Auth/LoginPage.vue"),
            },
            {
                name: "Register",
                path: "/register",
                component: () => import("../pages/Auth/RegisterPage.vue"),
                beforeEnter: (to, from) => {
                    if (store.getters['auth/isAuthenticated']) {
                        return false;
                    }
                }
            }
        ]
    },


    // Always leave this as last one,
    // but you can also remove it
    {
        path: "/:catchAll(.*)*",
        component: () => import("../pages/ErrorNotFound.vue"),
    },
];

export default routes;
