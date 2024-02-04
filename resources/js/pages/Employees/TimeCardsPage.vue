<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md">
            <div class="col-md-3 q-gutter-md">
                <q-date
                    minimal
                    emit-immediately
                    default-view="Years"
                    v-model="$form.year"
                    @update:model-value="onMonth"
                    mask="YYYY"
                    class="col"
                    :key="dpKey"
                />
                <q-date
                    minimal
                    emit-immediately
                    default-view="Months"
                    v-model="$form.month"
                    @update:model-value="onMonth"
                    mask="MMMM"
                    class="col"
                    :key="dpKey"
                />
            </div>
            <div class="col-md-9">
                <q-card>
                    <!-- Fields -->
                    <q-card-section v-if="!ui.loading">
                        <table style="width: 100%; max-width: 800px; margin: 0px auto;">
                            <thead>
                                <tr>
                                    <th colspan="7"><strong style="font-size: 20px;">Daily Time Record</strong></th>
                                </tr>
                                <tr>
                                    <th>For the Month of:</th>
                                    <th colspan="6">{{ $form.dtr.month }}</th>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <th colspan="6">{{ $form.fullname }}</th>
                                </tr>
                                <tr>
                                    <th colspan="7"></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th colspan="2">AM</th>
                                    <th colspan="2">PM</th>
                                    <th colspan="2">Undertime</th>
                                </tr>
                                <tr>
                                    <th colspan="7"></th>
                                </tr>
                                <tr>
                                    <th>Day</th>
                                    <th>Arrival</th>
                                    <th>Departure</th>
                                    <th>Arrival</th>
                                    <th>Departure</th>
                                    <th>Hour</th>
                                    <th>Minutes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example records -->
                                <tr v-for="(attendance, key) in $form.dtr?.days" :key="key">
                                    <td>{{ key }}</td>
                                    <td>{{ to12hr(attendance.in_am) }}</td>
                                    <td>{{ to12hr(attendance.out_am) }}</td>
                                    <td>{{ to12hr(attendance.in_pm) }}</td>
                                    <td>{{ to12hr(attendance?.out_pm) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </q-card-section>
                </q-card>
            </div>
        </q-form>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from 'vue-router'
import { useQuasar, date, useMeta } from 'quasar'
import _ from 'lodash'


useMeta({
    title: 'Employee Time Cards',
})

const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: true,
    yearOptions: [
        '2024',
        '2023',
        '2022',
        '2021',
        '2020',
        '2019',
    ],
    monthOptions: [
        'January', 'February', 'March', 'April', 'May', 'June','July', 'August', 'September', 'October', 'November', 'December'
    ]
})
const dpKey = ref()
const $form = ref({
    year: null,
    month: null,
    first_name: "",
    last_name: "",
    email: "",
});


onMounted(async ()=>{
    ui.loading = true
    const { data } = await axios.get(`/api/employees/${$route.params.id}`)
    $form.value = {...$form.value, ...data}
    ui.loading = false
})

function to12hr(time24hr) {
    if( typeof time24hr !== 'string' )
    return ''

    const [hour, minute] = time24hr.split(':');
    const parsedHour = parseInt(hour, 10);

    // Use the Date object to format the time
    const formattedTime = new Date(2000, 0, 1, parsedHour, minute);

    // Use Intl.DateTimeFormat to get the time in 12-hour format
    const time12hr = formattedTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });

    return time12hr;
}

async function onMonth(){
    dpKey.value = Date.now()
    ui.loading = true
    $q.loading.show()
    let params = {
        year: $form.value.year,
        month: $form.value.month
    }
    const { data } = await axios.get(`/api/employees/${$route.params.id}`, { params: params } )
    console.log( data )
    $form.value = {...$form.value, ...data}
    ui.loading = false
    $q.loading.hide()
}
</script>

<style scoped>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
