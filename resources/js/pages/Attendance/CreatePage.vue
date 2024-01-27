<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onCreate">
            <div class="col-md-3">
                <q-date v-model="$form.date" />
            </div>
            <div class="col-md-9">
                <q-card>
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <!-- Employee -->
                            <q-select
                                dense
                                outlined
                                use-input
                                emit-value
                                map-options
                                class="col-12"
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

                            <!-- In  AM-->
                            <q-input
                                dense
                                outlined
                                readonly
                                label="Time In AM"
                                class="col-6"
                                v-model="$form.in_am">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.in_am">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                            <!-- Out AM -->
                            <q-input
                                dense
                                outlined
                                readonly
                                label="Time Out AM"
                                class="col-6"
                                v-model="$form.out_am">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.out_am">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                            <!-- In  PM-->
                            <q-input
                                dense
                                outlined
                                readonly
                                label="Time In PM"
                                class="col-6"
                                v-model="$form.in_pm">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.in_pm">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                            <!-- Out PM -->
                            <q-input
                                dense
                                outlined
                                readonly
                                label="Time Out PM"
                                class="col-6"
                                v-model="$form.out_pm">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.out_pm">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                        </div>
                    </q-card-section>
                </q-card>

                <!-- Action -->
                <div class="row justify-end q-mt-md">
                    <div class="col-auto">
                        <q-btn
                            color="primary"
                            type="submit"
                            label="Create"
                            class="q-ml-md"
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
import _ from 'lodash'


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
    employees: [],
    employeeOptions: [],
})
const $form = reactive({
    date: new Date,
    in_am: "",
    out_am: "",
    in_pm: "",
    out_pm: "",
});


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


async function onCreate(){
    ui.loading = true
    try{
        const { data } = await axios.post(`/api/attendances`, $form)
        $q.notify({
            type: 'positive',
            message: `Created successfully!`
        })
        $router.push('/attendance')
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

</script>
