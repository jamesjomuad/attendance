<template>
    <q-page padding class="bg-grey-1">
        <div class="row q-col-gutter-md">
            <!-- Total Employees -->
            <div class="col-xs-12 col-sm-4 col-md-3">
                <q-card flat class="bg-grey-13 q-pa-sm">
                    <q-card-section>
                        <div class="flex justify-between">
                            <div>
                                <h2 class="text-h4 q-ma-none">{{ TotalEmployees }}</h2>
                                <div class="text-subtitle2">Total Employees</div>
                            </div>
                            <div>
                                <q-icon name="badge" size="5em"/>
                            </div>
                        </div>
                    </q-card-section>
                    <q-inner-loading :showing="ui.loading"></q-inner-loading>
                </q-card>
            </div>
            <!-- On Time Percentage -->
            <div class="col-xs-12 col-sm-4 col-md-3">
                <q-card flat class="bg-orange-13 q-pa-sm">
                    <q-card-section>
                        <div class="flex justify-between">
                            <div>
                                <h2 class="text-h4 q-ma-none">{{OnTimePercentage}}%</h2>
                                <div class="text-subtitle2">On Time Percentage</div>
                            </div>
                            <div>
                                <q-icon name="pie_chart" size="5em"/>
                            </div>
                        </div>
                    </q-card-section>
                    <q-inner-loading :showing="ui.loading"></q-inner-loading>
                </q-card>
            </div>
            <!-- On Time Today -->
            <div class="col-xs-12 col-sm-4 col-md-3">
                <q-card flat class="bg-yellow-13 q-pa-sm">
                    <q-card-section>
                        <div class="flex justify-between">
                            <div>
                                <h2 class="text-h4 q-ma-none">{{ OnTimeToday }}</h2>
                                <div class="text-subtitle2">On Time Today</div>
                            </div>
                            <div>
                                <q-icon name="more_time" size="5em"/>
                            </div>
                        </div>
                    </q-card-section>
                    <q-inner-loading :showing="ui.loading"></q-inner-loading>
                </q-card>
            </div>
            <!-- Late Today -->
            <div class="col-xs-12 col-sm-4 col-md-3">
                <q-card flat class="bg-red-13 q-pa-sm">
                    <q-card-section>
                        <div class="flex justify-between">
                            <div>
                                <h2 class="text-h4 q-ma-none">{{ LateToday }}</h2>
                                <div class="text-subtitle2">Late Today</div>
                            </div>
                            <div>
                                <q-icon name="timer_off" size="5em"/>
                            </div>
                        </div>
                    </q-card-section>
                    <q-inner-loading :showing="ui.loading"></q-inner-loading>
                </q-card>
            </div>
        </div>
    </q-page>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useMeta } from 'quasar'


useMeta({
    title: 'Dashboard',
})

const ui = reactive({
    loading: true
})
const TotalEmployees = ref()
const OnTimePercentage = ref()
const OnTimeToday = ref()
const LateToday = ref()

onMounted(async ()=>{
    ui.loading = true
    const { data } = await axios.get(`/api/statistics`)
    TotalEmployees.value = data.TotalEmployees
    OnTimePercentage.value = data.OnTimePercentage
    OnTimeToday.value = data.OnTimeToday
    LateToday.value = data.LateToday
    ui.loading = false
})

</script>
