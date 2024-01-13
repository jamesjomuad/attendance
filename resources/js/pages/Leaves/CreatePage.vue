<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onCreate">
            <div class="col-md-12">
                <q-card>
                    <!-- Fields -->
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <!-- Employee -->
                            <q-select
                                dense
                                outlined
                                use-input
                                emit-value
                                map-options
                                class="col-6"
                                label="Employee"
                                v-model="$form.employee"
                                input-debounce="600"
                                :options="ui.employees"
                                @filter="filterFn"
                                :rules="[val => !!val || 'Field is required']"
                                >
                                <template v-slot:no-option>
                                    <q-item>
                                    <q-item-section class="text-grey">
                                        No results
                                    </q-item-section>
                                    </q-item>
                                </template>
                            </q-select>

                            <!-- Type -->
                            <q-select
                                dense
                                outlined
                                class="col-6"
                                label="Type"
                                v-model="$form.type"
                                :options="ui.types"
                                :rules="[val => !!val || 'Field is required']"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="info" />
                                </template>
                            </q-select>

                            <!-- Date Start -->
                            <q-input
                                dense
                                outlined
                                label="Date Start"
                                class="col-6"
                                v-model="$form.start"
                                mask="date">
                                <template v-slot:append>
                                    <q-icon name="event" class="cursor-pointer">
                                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                            <q-date v-model="$form.start">
                                                <div class="row items-center justify-end">
                                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                                </div>
                                            </q-date>
                                        </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>

                            <!-- Date End -->
                            <q-input
                                dense
                                outlined
                                label="Date End"
                                class="col-6"
                                v-model="$form.end"
                                mask="date">
                                <template v-slot:append>
                                    <q-icon name="event" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                        <q-date v-model="$form.end">
                                        <div class="row items-center justify-end">
                                            <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                        </q-date>
                                    </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>
                            <!-- Reason -->
                            <q-input
                                v-model="$form.reason"
                                dense
                                outlined
                                label="Reason"
                                class="col-12"
                                type="textarea"
                                :rules="[val => !!val || 'Field is required']"
                            ></q-input>
                        </div>
                    </q-card-section>
                </q-card>
                <div class="row justify-end q-mt-md">
                    <div class="col-auto">
                        <q-btn
                            color="primary"
                            type="submit"
                            label="Create"
                            :disable="ui.loading"
                            :loading="ui.loading"
                        />
                    </div>
                </div>
            </div>
        </q-form>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import axios from "axios";


const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: true,
    employees: [],
    employeeOptions: [],
    types: [
        'Sick',
        'Vacation'
    ],
    status: ['approved', 'declined']
})
const $form = reactive({
    employee: null,
    type: null,
    start: new Date,
    end: new Date,
    reason: null,
},);


onMounted(async ()=>{
    try {
        const { data } = await axios.get(`/api/employees`)
        ui.employeeOptions = _.map(data.data, function(v){
            return {
                label: v.fullname,
                value: v.id,
            }
        })
    } catch (error) {
        $q.notify({
            color: 'negative',
            message: error.response?.statusText,
            icon: 'report_problem',
            position: 'bottom-right'
        })
    }
    ui.loading = false
})


function filterFn(val, update) {
    if (val === '') {
        update(() => {
            ui.employees = ui.employeeOptions
        })
        return
    }

    update(async () => {
        const keyword = val.toLowerCase()
        const { data } = await axios.get(`/api/employees?filter=${keyword}&limit=10&sortBy=id&orderBy=desc&page=1&per_page=10`)
        ui.employees = _.map(data.data, function(v){
            return {
                label: v.fullname,
                value: v.id,
            }
        })
    })
}

async function onCreate(){
    ui.loading = true
    try{
        const { data } = await axios.post(`/api/leaves`, $form)
        $q.notify({
            type: 'positive',
            message: `Created successfully!`
        })
        $router.push('/leaves')
    }catch(error){
        if(error.response?.data?.error){
            $q.notify({
                type: 'negative',
                message: error.response?.data?.error
            })
        }else{
            $q.notify({
                type: 'negative',
                message: "Error!"
            })
        }
    }
    ui.loading = false
}

</script>
