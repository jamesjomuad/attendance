<template>
    <q-layout view="hHh Lpr fFf" :class="{'bg-dark':$q.dark.isActive,'bg-light':!$q.dark.isActive}">
        <!-- Top Bar -->
        <q-header class="text-white bg-dark">
            <q-toolbar>
                <q-btn flat @click="miniState = !miniState" round dense icon="menu" />
                <q-toolbar-title v-if="!$q.screen.xs">{{ $route.meta?.title }} </q-toolbar-title>

                <q-space/>

                <q-toolbar-title shrink>
                    <span class="text-subtitle2">Hello, {{ user?.fullname }}</span>
                </q-toolbar-title>

                <q-btn round flat icon="account_circle">
                    <q-menu auto-close :offset="[110, 0]">
                        <q-list style="min-width: 150px">
                            <q-item clickable :to="`/`" target="_blank">
                                <q-item-section>Home</q-item-section>
                            </q-item>
                            <q-item clickable :to="`/system/users/${user.id}`">
                                <q-item-section>Account</q-item-section>
                            </q-item>
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
                    <menu-item label="Employees" icon="people" to="/employees"/>
                    <menu-item label="Positions" icon="badge" to="/positions"/>
                    <!-- <menu-item label="Departments" icon="work_outline" to="/departments"/> -->
                    <menu-item label="Attendance" icon="calendar_month" to="/attendance"/>
                    <menu-item label="Payroll" icon="credit_card" to="/payroll"/>

                    <q-separator />
                    <q-item-label header>Requests</q-item-label>
                    <menu-item label="Leaves" icon="spa" to="/leaves"/>
                    <q-separator />

                    <q-item-label header>System</q-item-label>
                    <menu-item label="Users" icon="people" to="/system/users"/>
                    <!-- <menu-item label="Settings" icon="settings" to="/system/settings"/> -->
                </q-list>
            </q-scroll-area>
        </q-drawer>

        <q-page-container :class="{'bg-grey-9':$q.dark.isActive,'bg-grey-2':!$q.dark.isActive}">
            <router-view />
        </q-page-container>
    </q-layout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useStore } from "vuex"
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import menuItem from "../components/MenuItem";



const $route = useRoute();
const $router = useRouter();
const $store = useStore();
const $q = useQuasar()
const drawer = ref(true)
const miniState = ref(false)
const user = computed(()=>$store.getters['auth/user'])

onMounted(()=>{
    if($q.screen.xs){
        drawer.value = false
    }
})

watch(miniState, (o,n)=>{
    if($q.screen.xs){
        drawer.value = true
    }
})

function onLogout(){
    console.log('logout')
    $store.commit('auth/logout')
    $router.push(`/login`)
}
</script>
