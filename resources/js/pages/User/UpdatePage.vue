<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="q-pa-md row q-col-gutter-md" @submit="onUpdate">
            <div class="col-md-12">
                <q-card>
                    <!-- Title -->
                    <q-card-section class="q-py-sm">
                        <div class="text-h6"> Account </div>
                    </q-card-section>

                    <!-- Fields -->
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <q-input
                                v-model="$form.username"
                                dense
                                outlined
                                label="Username"
                                name="username"
                                class="col-12"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="account_circle" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.first_name"
                                dense
                                outlined
                                label="First Name"
                                name="first_name"
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                <q-icon name="account_circle" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.last_name"
                                dense
                                outlined
                                lazy-rules
                                label="Last name *"
                                name="last_name"
                                class="col-6"
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
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-actions align="right">
                        <q-btn
                            color="negative"
                            label="Remove"
                            class="q-mr-auto"
                            @click="onRemove"
                            :disable="ui.loading"
                            :loading="ui.loading"
                        />
                        <q-btn
                            color="warning"
                            label="Reset Password"
                            @click="onChangePassword"
                            :disable="ui.resetting"
                            :loading="ui.resetting"
                        />
                        <q-btn
                            color="primary"
                            type="submit"
                            label="Update"
                            :disable="ui.loading"
                            :loading="ui.loading"
                        />
                    </q-card-actions>
                </q-card>
            </div>
        </q-form>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
    updating: false,
    resetting: false,
    removing: false
})
const $form = ref({
    first_name: "",
    last_name: "",
    email: "",
    billing_address: "",
    phone: "",
    phone_2: "",
});


onMounted(async ()=>{
    const { data } = await axios.get(`/api/users/${$route.params.id}`)
    $form.value = {...$form, ...data}
})


async function onUpdate(){
    ui.loading = true
    try{
        const { data } = await axios.put(`/api/users/${$route.params.id}`, $form.value)
        if(data.status){
            $q.notify({
                type: 'positive',
                message: `Updated successfully!`
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
}

function onRemove(){
    $q.dialog({
        title: 'Confirm',
        message: 'Would you like to procceed?',
        cancel: true,
        persistent: true
    }).onOk(async () => {
        ui.loading = true
        try{
            const { data } = await axios.post(`/api/users/${$route.params.id}`, {
                _method: 'delete'
            })
            if(data){
                $router.push('/system/users')
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
    })

}

function onChangePassword(){
    $q.dialog({
        title: 'Change password',
        prompt: {
            model: '',
            type: 'text' // optional
        },
        cancel: true,
        persistent: true
    }).onOk(async (password) => {
        ui.loading = true
        ui.resetting = true
        try{
            const { data } = await axios.put(`/api/users/${$route.params.id}`, {
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
            if(e.response.data?.error){
                $q.notify({
                    message: e.response.data?.error,
                    color: 'negative'
                })
            }else{
                $q.notify({
                    message: 'Error changing password',
                    color: 'negative'
                })
            }
        }
        ui.loading = false
        ui.resetting = false
    })
}

</script>
