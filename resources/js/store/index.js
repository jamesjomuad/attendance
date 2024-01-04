import { store } from 'quasar/wrappers'
import Vuex, { createStore } from "vuex";

import auth from './auth'

export default new Vuex.Store({
    modules: {
        auth,
    },
})
