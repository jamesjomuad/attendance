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
                            <q-select
                                dense
                                outlined
                                class="col-6"
                                label="Position"
                                v-model="$form.type"
                                :color="ui.typeBg"
                                :options="ui.positions"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="info" />
                                </template>
                            </q-select>

                            <q-select
                                dense
                                outlined
                                class="col-6"
                                label="Department"
                                v-model="$form.type"
                                :color="ui.typeBg"
                                :options="ui.departments"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="info" />
                                </template>
                            </q-select>

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
import { ref, reactive, } from "vue";
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
    isPwd: true,
    positions: ['Web Dev', 'Full Stack', 'CEO', 'HR'],
    departments: [
        'Human Resources',
        'Accounting',
        'Information Technology',
        'Marketing',
        'Sales',
        'Customer Service/Support',
        'Operations',
    ],
})
const $form = reactive({
    username: "",
    first_name: "",
    last_name: "",
    email: "",
    address: "",
    phone: "",
    phone_2: "",
},);


async function onCreate(){
    ui.loading = true
    try{
        $form.username = `${$form.first_name}.${$form.last_name}`
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
