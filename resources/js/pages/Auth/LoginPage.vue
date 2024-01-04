<template>
    <q-page
    class="bg-secondary flex flex-center"
    :class="{'xs':$q.screen.xs}"
    :style="{ background: ui.bg }"
    >
        <transition v-show="ui.showForm" appear enter-active-class="animate__animated animate__zoomIn animate__delay-2s">
            <form @submit.prevent="onLogin" class="shadow-10">
                <q-card
                    bordered
                    class="q-pa-lg shadow-1 animate__animated"
                    :class="{'animate__shakeX':ui.isInvalid}"
                    style="min-width: 450px"
                >
                    <q-card-section>
                    <img
                        alt="Quasar logo"
                        src="/images/logo.png"
                        style=" width: 150px; margin: 0 auto; display: block; margin-top: -100px; "
                        class="logo"
                    />
                    </q-card-section>
                    <q-card-section class="q-gutter-md">
                        <q-input
                        square
                        filled
                        clearable
                        v-model="$user.email"
                        label="Email"
                        :rules="[(val) => !!val || 'Field is required']"
                        />
                        <q-input
                        square
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
                    </q-card-section>
                    <q-card-actions class="q-px-md">
                    <q-btn
                        unelevated
                        color="accent"
                        size="lg"
                        class="full-width q-mb-md"
                        label="Login"
                        type="submit"
                        :loading="ui.btnLoginLoading"
                        :disable="ui.btnLoginLoading"
                    />
                    </q-card-actions>
                    <q-card-section class="text-center q-pa-none">
                    <p class="text-grey-6">
                        Not registered? <a href="#/register">Create Account</a>
                    </p>
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
const $user = reactive({ email: null, password: null });
const isPwd = ref(true);
const ui = reactive({
    bg: "url('/images/home-bg.jpg') 0% 100% / cover !important",
    btnLoginLoading: false,
    isInvalid: false,
    showForm: true
})

onMounted(()=>{
    if($store.getters['auth/isAuthenticated']){
        $router.push(`/`)
    }
})

async function onLogin(params) {
    ui.isInvalid = false
    ui.btnLoginLoading = true
    try {
        const { data } = await axios.post( "/api/auth/login", $user );
        if(data.status){
            let response = await axios.get( "/api/user", { headers: {"Authorization" : `Bearer ${data.token}`} });
            $store.commit('auth/setToken', data.token)
            $store.commit('auth/setUser', response.data)
            $router.push(`/`)
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
