<template>
    <q-page padding class="bg-grey-1">
        <div class="row q-gutter-md">
            <div class="col">
                <q-card flat class="bg-blue-3 q-pa-sm">
                    <q-card-section>
                        <div class="flex justify-between">
                            <div>
                                <h2 class="text-h4 q-ma-none">22</h2>
                                <div class="text-subtitle2">Total Employees</div>
                            </div>
                            <div>
                                <q-icon name="badge" size="5em"/>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col">
                <q-card flat class="bg-deep-orange-3 q-pa-sm">
                    <q-card-section>
                        <div class="flex justify-between">
                            <div>
                                <h2 class="text-h4 q-ma-none">57.78%</h2>
                                <div class="text-subtitle2">On Time Percentage</div>
                            </div>
                            <div>
                                <q-icon name="pie_chart" size="5em"/>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col">
                <q-card flat class="bg-yellow-3 q-pa-sm">
                    <q-card-section>
                        <div class="flex justify-between">
                            <div>
                                <h2 class="text-h4 q-ma-none">0</h2>
                                <div class="text-subtitle2">On Time Today</div>
                            </div>
                            <div>
                                <q-icon name="more_time" size="5em"/>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col">
                <q-card flat class="bg-red-3 q-pa-sm">
                    <q-card-section>
                        <div class="flex justify-between">
                            <div>
                                <h2 class="text-h4 q-ma-none">0</h2>
                                <div class="text-subtitle2">Late Today</div>
                            </div>
                            <div>
                                <q-icon name="timer_off" size="5em"/>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </q-page>
</template>

<script setup>
// import store from '../../store'
// const userRole = store.getters['auth/isCustomer'] ? 'customer' : 'admin'
import { useMeta } from 'quasar'


useMeta({
    title: 'Dashboard',
})
</script>
