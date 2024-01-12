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

    // MainLayout
    {
        path: "/dashboard",
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

            // Employees
            {
                path: "/employees",
                children: [{
                        path: "",
                        component: () => import("../pages/Employees/IndexPage.vue"),
                        meta: {
                            title: "Employees",
                            requiresAuth: true
                        }
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Employees/CreatePage.vue"),
                        meta: {
                            title: "New Employee",
                            requiresAuth: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Employees/UpdatePage.vue"),
                        meta: {
                            title: "Update Employees",
                            requiresAuth: true
                        }
                    },
                ]
            },

            // Positions
            {
                path: "/positions",
                children: [{
                        path: "",
                        component: () => import("../pages/Positions/IndexPage.vue"),
                        meta: {
                            title: "Positions",
                            requiresAuth: true
                        }
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Positions/CreatePage.vue"),
                        meta: {
                            title: "New Position",
                            requiresAuth: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Positions/UpdatePage.vue"),
                        meta: {
                            title: "Update Position",
                            requiresAuth: true
                        }
                    },
                ]
            },

            // Attendance
            {
                path: "/attendance",
                children: [{
                        path: "",
                        component: () => import("../pages/Attendance/IndexPage.vue"),
                        meta: {
                            title: "Attendance",
                            requiresAuth: true
                        }
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Attendance/CreatePage.vue"),
                        meta: {
                            title: "New Position",
                            requiresAuth: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Attendance/UpdatePage.vue"),
                        meta: {
                            title: "Update Position",
                            requiresAuth: true
                        }
                    },
                ]
            },

            // Payroll
            {
                path: "/payroll",
                children: [{
                        path: "",
                        component: () => import("../pages/Payroll/IndexPage.vue"),
                        meta: {
                            title: "Payroll",
                            requiresAuth: true
                        }
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Payroll/CreatePage.vue"),
                        meta: {
                            title: "New Position",
                            requiresAuth: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Payroll/UpdatePage.vue"),
                        meta: {
                            title: "Update Position",
                            requiresAuth: true
                        }
                    },
                ]
            },

            // Leaves
            {
                path: "/leaves",
                children: [{
                        path: "",
                        component: () => import("../pages/Leaves/IndexPage.vue"),
                        meta: {
                            title: "Leaves",
                            requiresAuth: true
                        }
                    },
                    {
                        path: "create",
                        component: () => import("../pages/Leaves/CreatePage.vue"),
                        meta: {
                            title: "New Position",
                            requiresAuth: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../pages/Leaves/UpdatePage.vue"),
                        meta: {
                            title: "Update Position",
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
