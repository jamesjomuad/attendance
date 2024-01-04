<template>
    <component :is="userRole === 'admin' ? AdminDashboard : CustomerDashboard" />
</template>

<script setup>
import store from '../../store'
import AdminDashboard from './IndexAdminPage.vue';
import CustomerDashboard from './IndexCustomerPage.vue';
const userRole = store.getters['auth/isCustomer'] ? 'customer' : 'admin'
</script>
