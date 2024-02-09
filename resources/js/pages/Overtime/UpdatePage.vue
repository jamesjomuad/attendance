<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onUpdate">
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

                            <!-- Date -->
                            <q-input
                                dense
                                outlined
                                label="Date"
                                class="col-6"
                                v-model="$form.date"
                                mask="date"
                                readonly>
                                <template v-slot:append>
                                    <q-icon name="event" class="cursor-pointer">
                                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                            <q-date v-model="$form.date">
                                                <div class="row items-center justify-end">
                                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                                </div>
                                            </q-date>
                                        </q-popup-proxy>
                                    </q-icon>
                                </template>
                            </q-input>

                            <!-- Start -->
                            <q-input
                                dense
                                outlined
                                readonly
                                label="Start Time"
                                class="col-6"
                                v-model="$form.start">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.start" mask="hh:mm A">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>

                            <!-- End -->
                            <q-input
                                dense
                                outlined
                                readonly
                                label="End Time"
                                class="col-6"
                                v-model="$form.end">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.end" mask="hh:mm A">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
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
                            color="negative"
                            label="Remove"
                            class="q-mr-md"
                            :disable="ui.removing"
                            :loading="ui.removing"
                            @click="onRemove"
                        />
                        <q-btn
                            color="primary"
                            type="submit"
                            label="Update"
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
import { useQuasar, useMeta } from 'quasar'

useMeta({
    title: 'Update Leave',
})

const $route = useRoute()
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
    ui.loading = true
    try {
        const { data } = await axios.get(`/api/employees`)
        ui.employees = _.map(data.data, function(v){
            return {
                label: v.fullname,
                value: v.id,
            }
        })
    } catch (error) {
        console.log(error)
        $q.notify({
            color: 'negative',
            message: error.response?.statusText,
            icon: 'report_problem',
            position: 'bottom-right'
        })
    }
})

onMounted(async()=>{
    const { data } = await axios.get(`/api/overtime/${$route.params.id}`)
    $form.employee = data.employee_id
    $form.date = data.date
    $form.start = data.startTime
    $form.end = data.endTime
    $form.reason = data.reason
    ui.loading = false
})


async function onUpdate(){
    ui.loading = true
    try{
        const { data } = await axios.put(`/api/overtime/${$route.params.id}`, $form)
        $q.notify({
            type: 'positive',
            message: `Updated successfully!`
        })
        $router.push('/overtime')
    }catch(error){
        console.log(error)
        $q.notify({
            type: 'negative',
            message: "Error!"
        })
    }
    ui.loading = false
}

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

function onRemove(){
    $q.dialog({
        title: 'Confirm',
        message: 'Would you like to procceed?',
        cancel: true,
        persistent: true
    }).onOk(async () => {
        ui.loading = true
        ui.removing = true
        try{
            const { data } = await axios.post(`/api/overtime/${$route.params.id}`, {
                _method: 'delete'
            })
            if(data){
                $router.push('/overtime')
                $q.notify({
                    type: 'positive',
                    message: `Remove successfully!`
                })
            }
        } catch(error){
            console.log(error)
            $q.notify({
                type: 'negative',
                message: "Error!"
            })
        }
        ui.loading = false
        ui.removing = false
    })

}

</script>
