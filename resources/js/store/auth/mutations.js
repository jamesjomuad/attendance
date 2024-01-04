import {  SessionStorage, LocalStorage } from 'quasar'

export function setUser(state, user){
    LocalStorage.set('user', user)
    state.user = user
}

export function setToken(state, token){
    LocalStorage.set('token', token)
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    state.token = token
}


export function logout(state){
    LocalStorage.set('token', false)
    state.user = null
    state.token = null
}
