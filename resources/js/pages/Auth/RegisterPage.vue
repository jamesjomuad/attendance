<template>
    <q-page class="flex flex-center bg-secondary" :style="{ background: ui.bg }">
        <q-card bordered class="q-pa-lg shadow-1 animate__animated animate__zoomIn animate__delay-2s" style="max-width: 750px">
        <!-- <q-card-section>
            <img
            alt="Quasar logo"
            src="~assets/logo.png"
            style="
                width: 150px;
                margin: 0 auto;
                display: block;
                margin-top: -100px;
            "
            />
        </q-card-section> -->
        <q-card-section>
            <q-form class="row q-col-gutter-md" @submit.prevent="onRegister">
            <div class="col-12">
                <q-input
                square
                filled
                v-model="$user.name"
                label="Username"
                :rules="validation.required"
                />
            </div>
            <div class="col-12">
                <q-input
                square
                filled
                v-model="$user.email"
                label="Email"
                type="email"
                :rules="validation.required"
                />
            </div>
            <div class="col-6">
                <q-input
                square
                filled
                v-model="$user.first_name"
                label="First Name"
                :rules="validation.required"
                />
            </div>
            <div class="col-6">
                <q-input
                square
                filled
                v-model="$user.last_name"
                label="Last Name"
                :rules="validation.required"
                />
            </div>
            <div class="col-6">
                <q-input
                square
                filled
                v-model="$user.password"
                label="Password"
                :rules="validation.required"
                :type="isPwd ? 'password' : 'text'"
                ><template v-slot:append>
                    <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                    /> </template
                ></q-input>
            </div>
            <div class="col-6">
                <q-input
                square
                filled
                v-model="$user.passwordConfirm"
                label="Confirm Password"
                :rules="validation.confirmPassword"
                :type="isPwd ? 'password' : 'text'"
                ><template v-slot:append>
                    <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                    /> </template
                ></q-input>
            </div>
            <div class="col-12">
                <q-btn
                unelevated
                color="accent"
                size="lg"
                class="full-width"
                label="Register"
                type="submit"
                :disable="ui.loading"
                :loading="ui.loading"
                />
            </div>
            </q-form>
        </q-card-section>
        <q-card-section class="text-center q-pa-none">
            <p class="text-grey-6">
            Already registered? <a href="#/">Login</a>
            </p>
        </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
import axios from "axios";
import { ref, reactive, computed, onMounted } from "vue";
import { useQuasar } from "quasar";
import { useRouter } from "vue-router";



const $q = useQuasar();
const $router = useRouter();
const isPwd = ref(true);
const ui = reactive({
    loading: false,
    bg: "url('/images/home-bg.jpg') 0% 100% / cover !important",
})
const $user = reactive({
    first_name: null,
    last_name: null,
    address: null,
    name: null,
    password: null,
    passwordConfirm: null,
});
const validation = reactive({
    confirmPassword: computed(() => [
        (val) => val == $user.password || "Password not matched!",
    ]),
    required: computed(() => [(val) => !!val || "Field is required"]),
});


async function onRegister() {
    ui.loading = true
    try {
        const { data } = await axios.post("/api/auth/register",$user);
        if (data.status) {
            $q.notify({
                message: data.message,
                color: "primary",
            })
            $router.push('/login')
        }
        else {
            $q.notify({
                message: data.message,
                color: "negative",
            })
        }
    }
    catch (error) {
        console.log(error);
    }
    ui.loading = false
}
</script>
