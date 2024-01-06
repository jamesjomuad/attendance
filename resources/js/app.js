require('./bootstrap');

import { createApp } from 'vue'
import { Quasar, LoadingBar, Loading, Notify, Dialog, LocalStorage, SessionStorage } from 'quasar'
import store from './store'
import router from "./router";
import App from "./App.vue";

// Initilize Vue 3
const app = createApp({
    ...App
});

app.use(store)

app.use(router())

// Quasar & Configs
app.use(Quasar, {
    animations: 'all',
    plugins: {
        Loading,
        LoadingBar,
        Notify,
        Dialog,
        LocalStorage,
        SessionStorage,
    },
    config: {
        loadingBar: {
            color: 'dark',
            size: '3px',
            position: 'top'
        },
        // brand: {
        //     primary: '#DA1F33',
        //     secondary: '#f1f1f1',
        //     accent: '#ff5f70'
        // }
    },
})

app.mount('#app')
