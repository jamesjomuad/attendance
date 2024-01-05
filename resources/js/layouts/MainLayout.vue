<template>
    <q-layout view="lHh Lpr lff" :class="{'bg-dark':$q.dark.isActive,'bg-light':!$q.dark.isActive}">
        <!-- Top Bar -->
        <q-header class="text-white bg-grey-9">
            <q-toolbar>
                <q-btn flat @click="drawer = !drawer" round dense icon="menu" />
                <q-toolbar-title v-if="!$q.screen.xs">{{ $route.meta?.title }} </q-toolbar-title>

                <q-space />

                <q-toolbar-title shrink>
                    <span class="text-subtitle2">Hello, {{ user?.fullname }}</span>
                </q-toolbar-title>

                <q-btn round flat icon="account_circle">
                    <q-menu auto-close :offset="[110, 0]">
                        <q-list style="min-width: 150px">
                            <q-item clickable @click.prevent="onLogout">
                                <q-item-section>Logout</q-item-section>
                            </q-item>
                        </q-list>
                    </q-menu>
                </q-btn>
            </q-toolbar>
        </q-header>

        <!-- Left Sidebar -->
        <q-drawer
            bordered
            v-model="drawer"
            :dark="$q.dark.isActive"
            :mini="miniState"
            :width="200"
            :breakpoint="500"
            :class="[{'bg-white': !$q.dark.isActive}]"
        >
            <q-scroll-area class="fit" :horizontal-thumb-style="{ opacity: 0 }">
                <q-list key="left-nav">
                    <menu-item label="Dashboard" icon="dashboard" to="/dashboard" exact/>
                    <q-separator />

                    <q-item-label header>Manage</q-item-label>
                    <menu-item v-if="hasAdminAccess" label="Employees" icon="people" to="/employees"/>
                    <menu-item v-if="hasAdminAccess" label="Positions" icon="badge" to="/positions"/>
                    <!-- <menu-item v-if="hasAdminAccess" label="Departments" icon="work_outline" to="/departments"/> -->
                    <menu-item v-if="hasAdminAccess" label="Attendance" icon="calendar_month" to="/attendance"/>
                    <menu-item v-if="hasAdminAccess" label="Payroll" icon="credit_card" to="/payroll"/>
                    <q-separator />

                    <q-item-label v-if="hasAdminAccess" header>System</q-item-label>
                    <menu-item v-if="hasAdminAccess" label="Users" icon="people" to="/system/users"/>
                    <menu-item v-if="hasAdminAccess" label="Settings" icon="settings" to="/system/settings"/>
                </q-list>
            </q-scroll-area>
        </q-drawer>

        <q-page-container :class="{'bg-grey-9':$q.dark.isActive,'bg-grey-2':!$q.dark.isActive}">
            <router-view />
        </q-page-container>
    </q-layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from "vuex"
import { useRoute, useRouter } from 'vue-router'
import { copyToClipboard, debounce } from 'quasar'
import menuItem from "../components/MenuItem";



const $route = useRoute();
const $router = useRouter();
const $store = useStore();
const drawer = ref(true)
const miniState = ref(false)
const user = computed(()=>$store.getters['auth/user'])
const hasAdminAccess = ref(!$store.getters['auth/isCustomer'])


function onLogout(){
    console.log('logout')
    $store.commit('auth/logout')
    $router.push(`/login`)
}
</script>
