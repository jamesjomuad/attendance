<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="q-pa-md row q-col-gutter-md" @submit="onCreate">
            <div class="col-md-12">
                <q-card>
                    <!-- Fields -->
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <q-input
                                v-model="$form.username"
                                dense
                                outlined
                                label="Username"
                                name="username"
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="account_circle" />
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


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
    updating: false,
    resetting: false,
    removing: false,
    isPwd: true
})
const $form = ref({
    username: "",
    password: null,
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
});



async function onCreate(){
    ui.loading = true
    try{
        const { data } = await axios.post(`/api/users`, $form.value)
        if(data.status){
            $q.notify({
                type: 'positive',
                message: data.message
            })
            $router.push(`/system/users/${data.data.id}`)
        }
    }
    catch(error){
        console.log(error)
        $q.notify({
            type: 'negative',
            message: error.response.data.error
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
