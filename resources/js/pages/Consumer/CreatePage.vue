<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onCreate">
            <div class="col-md-12">
                <q-card>
                <!-- Title -->
                <q-card-section class="q-py-sm">
                    <div class="text-h6">
                    Account
                    </div>
                </q-card-section>
                <!-- Fields -->
                <q-card-section>
                    <div class="row q-col-gutter-md">
                        <q-input
                            dense
                            outlined
                            v-model="$form.name"
                            label="Username"
                            class="col-6"
                            :rules="[ val => val && val.length > 0 || 'Required!']"
                        >
                            <template v-slot:prepend>
                            <q-icon name="perm_identity" />
                            </template>
                        </q-input>

                        <q-input
                            dense
                            outlined
                            v-model="$form.password"
                            label="Password"
                            class="col-6"
                            :rules="[ val => val && val.length > 0 || 'Required!']"
                            :type="ui.isPwd ? 'password' : 'text'"
                            ><template v-slot:append>
                                <q-icon
                                :name="ui.isPwd ? 'visibility_off' : 'visibility'"
                                class="cursor-pointer"
                                @click="ui.isPwd = !ui.isPwd"
                                /> </template
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

                        <q-input
                            v-model="$form.phone_2"
                            dense
                            outlined
                            label="Work Phone"
                            lazy-rules
                            class="col-6"
                        >
                            <template v-slot:prepend>
                            <q-icon name="phone" />
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
                            v-model="$form.dob"
                            dense
                            outlined
                            label="Date of Birth"
                            lazy-rules
                            class="col-6"
                            readonly
                        >
                            <template v-slot:prepend>
                            <q-icon name="calendar_month" />
                            </template>
                            <template v-slot:after>
                                <q-btn icon="event" round color="primary">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                        <q-date v-model="$form.dob">
                                        <div class="row items-center justify-end q-gutter-sm">
                                            <q-btn label="Cancel" color="primary" flat v-close-popup />
                                            <q-btn label="OK" color="primary" flat v-close-popup />
                                        </div>
                                        </q-date>
                                    </q-popup-proxy>
                                </q-btn>
                            </template>
                        </q-input>

                        <q-input
                            v-model="$form.barangay"
                            dense
                            outlined
                            label="Barangay"
                            lazy-rules
                            class="col-6"
                        >
                            <template v-slot:prepend>
                                <q-icon name="place" />
                            </template>
                        </q-input>

                        <q-input
                            v-model="$form.sitio"
                            dense
                            outlined
                            label="Sitio"
                            lazy-rules
                            class="col-6"
                        >
                            <template v-slot:prepend>
                                <q-icon name="place" />
                            </template>
                        </q-input>

                        <q-input
                            v-model="$form.billing_address"
                            dense
                            outlined
                            label="Billing Address"
                            lazy-rules
                            class="col-12"
                        >
                            <template v-slot:prepend>
                                <q-icon name="home" />
                            </template>
                        </q-input>
                    </div>
                </q-card-section>
                <q-separator />
                <q-card-actions align="right">
                    <q-btn
                    color="primary"
                    type="submit"
                    :disable="ui.loading"
                    :loading="ui.loading"
                    >
                    Create
                    </q-btn>
                </q-card-actions>
                </q-card>
            </div>
        </q-form>
    </q-page>
</template>


<script setup>
import { ref, reactive, } from "vue";
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
    isPwd: true
})
const $form = reactive({
    first_name: "",
    last_name: "",
    email: "",
    billing_address: "",
    phone: "",
    phone_2: "",
},);


async function onCreate(){
    ui.loading = true
    try{
        const { data } = await axios.post(`/api/consumers`, $form)
        $q.notify({
            type: 'positive',
            message: `${data.data.first_name} ${data.data.last_name} created successfully!`
        })
        $router.push('/consumers')
    }catch(error){
        console.log(error)
        $q.notify({
            type: 'negative',
            message: "Error!"
        })
    }
    ui.loading = false
}

</script>
