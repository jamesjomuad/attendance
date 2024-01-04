import {  LocalStorage } from 'quasar'

export function isAuthenticated(state){
    state.token = LocalStorage.getItem('token')
    return !!state.token
}

export function token(state){
    state.token = LocalStorage.getItem('token')
    return state.token
}

export function user(state){
    state.user = LocalStorage.getItem('user')
    return state.user
}


export function isCustomer(state){
    state.user = LocalStorage.getItem('user')
    return state.user?.is_consumer
}

