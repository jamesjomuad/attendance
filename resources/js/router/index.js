import { route } from 'quasar/wrappers'
import { createRouter, createWebHashHistory } from 'vue-router'
import routes from './routes'
import store from '../store'


export default route(function (/* { store, ssrContext } */) {
    const Router = createRouter({
        scrollBehavior: () => ({ left: 0, top: 0 }),
        routes,

        history: createWebHashHistory()
    })

    Router.beforeEach((to, from, next) => {
        if (to.matched.some((record) => record.meta.requiresAuth)) {
            if (store.getters['auth/isAuthenticated']) {
                next();
                return;
            }
            next("/login");
        }
        else {
            next();
        }
    })

    return Router
})
