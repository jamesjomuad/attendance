<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md">
            <div class="col-md-12">
                <q-card>
                    <!-- Fields -->
                    <q-card-section>
                        <table style="width: 100%; max-width: 800px; margin: 0px auto;">
                            <thead>
                                <tr>
                                    <th colspan="7">Daily Time Record</th>
                                </tr>
                                <tr>
                                    <th>For the Month of:</th>
                                    <th colspan="6">January</th>
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
                                    <th>AM</th>
                                    <th></th>
                                    <th>PM</th>
                                    <th></th>
                                    <th>Undertime</th>
                                    <th></th>
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
                                <tr v-for="(attendance, key) in $form.attendance" :key="key">
                                    <td></td>
                                    <td>2024-01-24</td>
                                    <td>09:00 AM</td>
                                    <td>05:00 PM</td>
                                    <td>8.0</td>
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
    loading: false,
})
const $form = ref({
    first_name: "",
    last_name: "",
    email: "",
});


onMounted(async ()=>{
    ui.loading = true
    const { data } = await axios.get(`/api/employees/${$route.params.id}`)
    $form.value = {...$form.value, ...data}
    console.log( $form.value )
    ui.loading = false
})
</script>

<style scoped>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
