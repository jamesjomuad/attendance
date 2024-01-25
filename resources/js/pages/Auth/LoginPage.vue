<template>
    <q-page class="flex flex-center bg-dark" :class="{'xs':$q.screen.xs}">
        <transition v-show="ui.showForm" appear enter-active-class="animate__animated animate__zoomIn animate__delay-1s">
            <form @submit.prevent="onLogin" class="shadow-10">
                <q-card
                    flat
                    class="q-pa-lg shadow-1 animate__animated"
                    :class="{'animate__shakeX':ui.isInvalid}"
                    style="min-width: 450px"
                >
                    <q-card-section class="q-gutter-md">
                        <!-- Username -->
                        <q-input
                            filled
                            clearable
                            class="bg-light"
                            v-model="$user.username"
                            label="Username"
                            :rules="[(val) => !!val || 'Field is required']"
                        />
                        <!-- Password -->
                        <q-input
                        filled
                        v-model="$user.password"
                        label="Password"
                        :rules="[(val) => !!val || 'Field is required']"
                        :type="isPwd ? 'password' : 'text'"
                        ><template v-slot:append>
                            <q-icon
                            :name="isPwd ? 'visibility_off' : 'visibility'"
                            class="cursor-pointer"
                            @click="isPwd = !isPwd"
                            /> </template
                        ></q-input>
                        <div class="col">
                            <q-btn
                                unelevated
                                color="primary"
                                class="full-width q-mb-md"
                                label="Login"
                                type="submit"
                                :loading="ui.btnLoginLoading"
                                :disable="ui.btnLoginLoading"
                            />
                        </div>
                    </q-card-section>
                </q-card>
            </form>
        </transition>
    </q-page>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";
import { useStore } from "vuex"
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const $store = useStore();
const $router = useRouter();
const $user = reactive({ username: null, password: null });
const isPwd = ref(true);
const ui = reactive({
    bg: "url('/images/home-bg.jpg') 0% 100% / cover !important",
    btnLoginLoading: false,
    isInvalid: false,
    showForm: true
})

onMounted(()=>{
    if($store.getters['auth/isAuthenticated']){
        $router.push(`/dashboard`)
    }
})

async function onLogin(params) {
    ui.isInvalid = false
    ui.btnLoginLoading = true
    try {
        const { data } = await axios.post( "/api/auth/login", $user );
        if(data.status){
            $store.commit('auth/setToken', data.token)
            $store.commit('auth/setUser', data.data)
            $router.push(`/dashboard`)
        }
    }
    catch (error) {
        ui.isInvalid = true
        $q.notify({
            message: error.response.data.message,
            color: "negative"
        })
    }
    ui.btnLoginLoading = false
}
</script>


<style>
.xs .logo{margin: 0 auto!important;}
.xs .q-card{min-width: 100%!important;}
</style>
