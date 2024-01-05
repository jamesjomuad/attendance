<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onUpdate">
            <div class="col-md-12">
                <q-card>
                    <!-- Fields -->
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <q-input
                                v-model="$form.code"
                                dense
                                outlined
                                label="Code"
                                class="col-12"
                                readonly
                            ></q-input>

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
                                label="Rate"
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

                <!-- Action -->
                <div class="row justify-end q-mt-md">
                    <div class="col-auto">
                        <q-btn
                            color="negative"
                            label="Remove"
                            :disable="ui.removing"
                            :loading="ui.removing"
                            @click="onRemove"
                        />
                        <q-btn
                            color="primary"
                            type="submit"
                            label="Save"
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
import { useQuasar, date } from 'quasar'
import _ from 'lodash'


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
    updating: false,
    resetting: false,
    removing: false,
    positions: [],
    departments: [],
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
    $form.value.first_name = data?.user.first_name
    $form.value.last_name = data?.user.last_name
    $form.value.schedule_in = date.formatDate($form.value.schedule_in, 'hh:mm A')
    $form.value.schedule_out = date.formatDate($form.value.schedule_out, 'hh:mm A')
    await getPositions()
    ui.loading = false
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


async function onUpdate(){
    ui.loading = true
    ui.updating = true
    try{
        const { data } = await axios.put(`/api/employees/${$route.params.id}`, $form.value)
        if(data.message){
            $q.notify({
                type: 'positive',
                message: data.message
            })
        }
    }
    catch(error){
        console.log(error)
        $q.notify({
            type: 'negative',
            message: "Error!"
        })
    }
    ui.loading = false
    ui.updating = false
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
            const { data } = await axios.post(`/api/employees/${$route.params.id}`, {
                _method: 'delete'
            })
            if(data){
                $router.push('/employees')
                $q.notify({
                    type: 'positive',
                    message: `${$form.value.first_name} ${$form.value.last_name} remove successfully!`
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

async function onResetPassword(){
    $q.dialog({
        title: 'Set new password',
        prompt: {
            model: '',
            type: 'password',
            outlined: true,
            flat: true
        },
        cancel: true,
        persistent: true
    }).onOk(async(password) => {
        ui.loading = true
        ui.resetting = true
        try{
            const { data } = await axios.put(`/api/employees/${$route.params.id}`, {
                ...$form.value,
                password: password
            })
            if(data.status){
                $q.notify({
                    type: 'positive',
                    message: `Password updated!`
                })
            }
        }
        catch(e){
            $q.notify({
                message: 'Error changing password',
                color: 'negative'
            })
        }
        ui.loading = false
        ui.resetting = false
    })
}
</script>

