<template>
    <q-page padding>
        <div class="row q-mb-lg q-col-gutter-md">
            <!-- Monthly Consumptions -->
            <div class="col-xs-12 col-sm-6 col-md-6">
                <q-card flat bordered class="radius-8" style="min-height:400px;">
                    <q-card-section>
                        <apexcharts
                            height="350"
                            type="bar"
                            :options="chartMonthly.options"
                            :series="chartMonthly.series"
                        ></apexcharts>
                    </q-card-section>
                    <q-inner-loading :showing="ui.loading"/>
                </q-card>
            </div>

            <!-- Weekly Consumptions -->
            <div class="col-xs-12 col-sm-6 col-md-6">
                <q-card flat bordered class="radius-8" style="min-height:400px;">
                    <q-card-section>
                        <apexcharts
                            height="350"
                            type="bar"
                            :options="chartWeekly.options"
                            :series="chartWeekly.series"
                        ></apexcharts>
                    </q-card-section>
                    <q-inner-loading :showing="ui.loading"/>
                </q-card>
            </div>

            <!-- Daily Consumptions -->
            <div class="col-xs-12 col-sm-6 col-md-6">
                <q-card flat bordered class="radius-8" style="min-height:400px;">
                    <q-card-section>
                        <apexcharts
                            height="350"
                            type="bar"
                            :options="chartDaily.options"
                            :series="chartDaily.series"
                        ></apexcharts>
                    </q-card-section>
                    <q-inner-loading :showing="ui.loading"/>
                </q-card>
            </div>

            <!-- Top Consumers -->
            <!-- <div class="col-xs-12 col-sm-6 col-md-6">
                <q-card flat bordered class="radius-8">
                    <q-card-section>
                        <div class="flex justify-between">
                            <h2 class="text-h6 q-ma-none">Top Consumers</h2>
                            <div class="text-subtitle2">This Month</div>
                        </div>
                    </q-card-section>
                    <q-card-section>
                        <q-list bordered dense separator v-if="consumers.list">
                            <q-item v-for="(customer, k) in consumers.list" :key="k">
                                <q-item-section>{{ customer }}</q-item-section>
                            </q-item>
                        </q-list>
                    </q-card-section>
                    <q-inner-loading :showing="consumers.loading"/>
                </q-card>
            </div> -->
        </div>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import apexcharts from "vue3-apexcharts";
import _ from 'lodash'

const ui = reactive({
    loading: true
})
const loading = ref(false)
const chartMonthly = reactive({
    options: {
        chart: {
            id: "monthly-consumption",
        },
        title: {
            text: 'Monthly Consumptions', // Set your chart title here
            align: 'left', // You can also specify alignment (left, center, right)
        },
        xaxis: {
            categories: [],
        },
    },
    series: [
        {
            name: "Month",
            data: [0],
        }
    ],
})
const chartWeekly = reactive({
    options: {
        chart: {
            id: "weekly-consumption",
        },
        title: {
            text: 'Weekly Consumptions', // Set your chart title here
            align: 'left', // You can also specify alignment (left, center, right)
        },
        xaxis: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        },
    },
    series: [
        {
            name: "weeks",
            data: [0,0,0,0,0,0,0],
        },
    ],
})
const chartDaily = reactive({
    options: {
        chart: {
            id: "daily-consumption",
        },
        title: {
            text: 'Daily Consumptions (Past 12Hrs)', // Set your chart title here
            align: 'left', // You can also specify alignment (left, center, right)
        },
        xaxis: {
            categories: [],
        },
    },
    series: [
        {
            name: "weeks",
            data: [0,0,0,0,0,0,0,0,0,0,0,0],
        },
    ],
})
// const consumers = reactive({
//     loading: false,
//     list: ["Adam", "Alex", "Aaron", "Ben", "Carl", "Dan", "David", "Edward", "Fred"]
// })


onMounted(async ()=>{
    const { data } = await axios.get(`/api/dashboard`)

    updateMonthly(data.monthly)

    updateWeekly(data.weekly)

    updateDaily(data.daily)

    ui.loading = false
})

function updateDaily(data){
    chartDaily.options = {
        xaxis: {
            categories: _.map(data, (v,k)=>k),
        },
    }

    chartDaily.series = [
        {
            name: "Daily",
            data: _.map(data, (v,k)=>v)
        }
    ]
}

function updateMonthly(data){
    chartMonthly.series = [
        {
            name: "Month",
            data: _.map(data, (v,k)=>v)
        }
    ]

    chartMonthly.options = {
        xaxis: {
            categories: _.map(data, (v,k)=>k),
        },
    }
}

function updateWeekly(data){
    chartWeekly.options = {
        xaxis: {
            categories: _.map(data, (v,k)=>k),
        },
    }

    chartWeekly.series = [
        {
            name: "weeks",
            data: _.map(data, (v,k)=>v)
        }
    ]
}

</script>
