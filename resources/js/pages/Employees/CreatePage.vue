<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onCreate">
            <div class="col-md-12">
                <q-card>
                    <!-- Fields -->
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <q-input
                                v-model="$form.first_name"
                                dense
                                outlined
                                label="First Name"
                                name="first_name"
                                class="col-6"
                                :rules="[ val => val && val.length > 0 || 'Please type something']"
                            >
                                <template v-slot:prepend>
                                <q-icon name="account_circle" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.last_name"
                                dense
                                outlined
                                label="Last Name"
                                name="last_name"
                                lazy-rules
                                class="col-6"
                                :rules="[ val => val && val.length > 0 || 'Please type something']"
                            >
                                <template v-slot:prepend>
                                <q-icon name="account_circle" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.email"
                                dense
                                outlined
                                label="Email"
                                lazy-rules
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                <q-icon name="email" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.phone"
                                dense
                                outlined
                                label="Phone"
                                lazy-rules
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                <q-icon name="phone" />
                                </template>
                            </q-input>

                            <q-select
                                dense
                                outlined
                                class="col-6"
                                label="Gender"
                                v-model="$form.gender"
                                :color="ui.typeBg"
                                :options="['Male', 'Female']"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="info" />
                                </template>
                            </q-select>

                            <q-input
                                v-model="$form.address"
                                dense
                                outlined
                                label="Address"
                                lazy-rules
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="home" />
                                </template>
                            </q-input>
                        </div>
                    </q-card-section>
                </q-card>

                <!-- Work Details -->
                <q-card class="q-mt-md">
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <!-- Position -->
                            <q-select
                                dense
                                outlined
                                class="col-6"
                                label="Position"
                                v-model="$form.position"
                                :color="ui.typeBg"
                                :options="ui.positions"
                                emit-value
                                map-options
                            >
                                <template v-slot:prepend>
                                    <q-icon name="info" />
                                </template>
                            </q-select>
                            <!-- Rate -->
                            <q-input
                                v-model="$form.rate"
                                dense
                                outlined
                                label="Hourly rate"
                                lazy-rules
                                class="col-6"
                                name="rate"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="paid" />
                                </template>
                            </q-input>
                            <!-- Schedule In -->
                            <q-input
                                dense
                                outlined
                                label="Schedule In"
                                class="col-6"
                                v-model="$form.schedule_in">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.schedule_in" mask="hh:mm A">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                            <!-- Schedule Out -->
                            <q-input
                                dense
                                outlined
                                label="Schedule Out"
                                class="col-6"
                                v-model="$form.schedule_out">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.schedule_out" mask="hh:mm A">
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
                <div class="row justify-end q-mt-md">
                    <div class="col-auto">
                        <q-btn
                            color="primary"
                            type="submit"
                            label="Save"
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
    isPwd: true,
    positions: [],
    departments: [],
})
const $form = reactive({
    username: "",
    first_name: "",
    last_name: "",
    email: "",
    address: "",
    phone: "",
    schedule_in: "8:00 AM",
    schedule_out: "5:00 PM",
},);


onMounted(async ()=>{
    getPositions()
})

async function getPositions(){
    try {
        const { data } = await axios.get(`/api/positions`)
        ui.positions = _.map(data.data, function(v){
            return {
                label: v.title,
                value: v.id,
            }
        })
    } catch (e) {
        console.log( e )
    }
}


async function onCreate(){
    ui.loading = true
    try{
        $form.username = `${$form.first_name}.${$form.last_name}`
        const { data } = await axios.post(`/api/employees`, $form)
        $q.notify({
            type: 'positive',
            message: `${data.data.first_name} ${data.data.last_name} created successfully!`
        })
        $router.push('/employees')
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
