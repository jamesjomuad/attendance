<template>
    <q-page padding>
        <div class="row q-mb-lg q-col-gutter-md">

            <!-- Announcement -->
            <div class="col-xs-12 col-sm-6 col-md-6">
                <q-card flat bordered class="radius-8">
                    <q-card-section>
                        <div class="flex justify-between">
                            <h2 class="text-h6 q-ma-none">Announcements</h2>
                            <!-- <div class="text-subtitle2">This Month</div> -->
                        </div>
                    </q-card-section>
                    <q-card-section>
                        <q-scroll-area style="height: 300px;">
                            <q-list>
                                <template v-for="(item, i) in announcements.data" :key="i">
                                    <q-item clickable v-ripple @click="onAnnouncement(item)">
                                        <q-item-section>
                                            <q-item-label>{{ item.title }}</q-item-label>
                                            <q-item-label caption lines="2" v-html="item.content"></q-item-label>
                                        </q-item-section>
                                        <q-item-section side top>
                                            <q-icon
                                                :name="announcements.icon(item.type)"
                                                :color="announcements.color(item.type)"
                                            />
                                            <q-item-label caption>{{ item.time }}</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                    <q-separator spaced inset />
                                </template>
                            </q-list>
                        </q-scroll-area>
                    </q-card-section>
                </q-card>
            </div>

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
        </div>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import apexcharts from "vue3-apexcharts";
import { useQuasar } from 'quasar'
import _ from 'lodash'


const $q = useQuasar()
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
const announcements = reactive({
    data: [],
    icon (type) {
        let icons = {
            'Emergency': 'error',
            'Alert': 'warning',
            'Update': 'tips_and_updates',
            'Reminder': 'notifications'
        }
        return icons[type]
    },
    color (type) {
        let colors = {
            'Emergency': 'red',
            'Alert': 'warning',
            'Update': 'green-4',
            'Reminder': 'info'
        }
        return colors[type]
    }
})


onMounted(async ()=>{
    const { data } = await axios.get(`/api/dashboard`)

    updateMonthly(data.monthly)

    updateWeekly(data.weekly)

    updateDaily(data.daily)

    announcements.data = await getAnnouncements()

    console.log(announcements.data)

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

async function getAnnouncements(){
    const params = {
        sortBy: 'id',
        orderBy: "desc",
        // per_page: rowsPerPage
    };
    const { data } = await axios.get(`/api/announcement`, {params})
    return data.data
}

function onAnnouncement(announcement){
    console.log(announcement)
    $q.dialog({
        title: announcement.title,
        message: announcement.content,
        html: true
    }).onOk(() => {
        // console.log('OK')
    })
}

</script>
