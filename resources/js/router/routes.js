import store from '../store'

const routes = [
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

    // MainLayout
    {
        path: "/",
        component: route => import('../layouts/MainLayout.vue'),
        children: [
            // Dashboard
            {
                path: "",
                component: () => import("../pages/Dashboard/IndexPage.vue"),
                meta: {
                    title: "Dashboard",
                    requiresAuth: true
                }
            },

            // Billing
            {
                path: "/billing",
                children: [{
                        path: "",
                        component: () => import("../pages/Billing/IndexPage.vue"),
                        meta: {
                            title: "Billing",
                            requiresAuth: true
                        },
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Consumption/CreatePage.vue"),
                        meta: {
                            title: "Billing Update",
                            requiresAuth: true
                        },
                    },
                ]
            },

            // Transactions
            {
                path: "/transactions",
                children: [
                    {
                        path: "",
                        component: () => import("../pages/Transaction/IndexPage.vue"),
                        meta: {
                            title: "Transactions",
                            requiresAuth: true
                        },
                    }
                ]
            },

            // Consumers
            {
                path: "/consumers",
                children: [{
                        path: "",
                        component: () => import("../pages/Consumer/IndexPage.vue"),
                        meta: {
                            title: "Consumers",
                            requiresAuth: true
                        }
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Consumer/CreatePage.vue"),
                        meta: {
                            title: "New Consumer",
                            requiresAuth: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Consumer/UpdatePage.vue"),
                        meta: {
                            title: "Update Consumer",
                            requiresAuth: true
                        }
                    },
                ]
            },

            // Consumptions
            {
                path: "/consumptions",
                children: [{
                        path: "",
                        component: () => import("../pages/Consumption/IndexPage.vue"),
                        meta: {
                            title: "Consumptions",
                            requiresAuth: true
                        }
                    },
                    {
                        path: "create/:id",
                        component: () => import("../pages/Consumption/CreatePage.vue"),
                        meta: {
                            title: "Create",
                            requiresAuth: true
                        }
                    }
                ]
            },

            // Announcement
            {
                path: "/system/announcement",
                children: [
                    {
                        path: "",
                        component: () => import("../pages/Announcement/IndexPage.vue"),
                        meta: {
                            title: "Announcement",
                            requiresAuth: true
                        }
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Announcement/CreatePage.vue"),
                        meta: {
                            title: "New Announcement",
                            requiresAuth: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Announcement/UpdatePage.vue"),
                        meta: {
                            title: "Update Announcement",
                            requiresAuth: true
                        }
                    },
                ]
            },

            // Users
            {
                path: "/system/users",
                children: [
                    {
                        path: "",
                        component: () => import("../pages/User/IndexPage.vue"),
                        meta: {
                            title: "Users",
                            requiresAuth: true
                        },
                    },
                    {
                        path: "create",
                        component: () => import("../pages/User/CreatePage.vue"),
                        meta: {
                            title: "Create User",
                            requiresAuth: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/User/UpdatePage.vue"),
                        meta: {
                            title: "Update User",
                            requiresAuth: true
                        }
                    }
                ]
            },

            // Settings
            {
                path: "/system/settings",
                children: [
                    {
                        path: "",
                        component: () => import("../pages/Setting/IndexPage.vue"),
                        meta: {
                            title: "Settings",
                            requiresAuth: true
                        },
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Setting/CreatePage.vue"),
                        meta: {
                            title: "Settings",
                            requiresAuth: true
                        },
                    }
                ]
            },


            // Roles
            {
                path: "/system/roles",
                children: [
                    {
                        path: "",
                        component: () => import("../pages/Role/IndexPage.vue"),
                        meta: {
                            title: "Roles",
                            requiresAuth: true
                        },
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Role/CreatePage.vue"),
                        meta: {
                            title: "Roles",
                            requiresAuth: true
                        },
                    }
                ]
            },


            /*
            /*  Customer Routes
            */

            //  Customer Dashboard
            {
                path: "",
                component: () => import("../pages/Dashboard/IndexCustomerPage.vue"),
                meta: {
                    title: "Dashboard",
                    requiresAuth: true,
                    role: 'consumer'
                }
            },
            // Customer Billing
            {
                path: "/billing",
                children: [{
                        path: "",
                        component: () => import("../pages/Billing/IndexPage.vue"),
                        meta: {
                            title: "Billing",
                            requiresAuth: true,
                            role: 'consumer'
                        },
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Consumption/CreatePage.vue"),
                        meta: {
                            title: "Billing Update",
                            requiresAuth: true,
                            role: 'consumer'
                        },
                    },
                ]
            },
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
