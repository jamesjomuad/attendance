<template>
    <q-page class="flex flex-center bg-dark">
        <div id="clock" class="column">
            <p class="col date">{{ ui.date }}</p>
            <p class="col time">{{ ui.time }}</p>

            <div class="row">
                <div class="col">
                    <q-input
                        dense
                        outlined
                        rounded
                        dark
                        :bg-color="employeeCodeBg"
                        label="Employee #"
                        v-model="employeeCode"
                        class="animate__animated"
                        :class="employeeCodeClass"
                        :rules="[ val => val && val.length > 0 || 'Please type something']"
                    >
                        <template v-slot:prepend>
                            <q-icon name="account_circle" />
                        </template>
                    </q-input>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <q-btn
                        color="primary"
                        size="lg"
                        icon="login"
                        label="Time In"
                        :loading="ui.inLoading"
                        @click="onTimeIn"
                    />
                </div>
                <div class="col">
                    <q-btn
                        color="secondary"
                        size="lg"
                        icon-right="logout"
                        label="Time Out"
                        :loading="ui.outLoading"
                        @click="onTimeOut"
                    />
                </div>
            </div>

            <div class="row justify-center">
                <div class="col-auto">
                    <qrcode-stream
                        style="width: 250px;"
                        @detect="onDetect"
                        @error="onError"
                    />
                </div>
            </div>
        </div>
    </q-page>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useQuasar, date, debounce } from "quasar";
import { useLink, useRouter } from "vue-router";
import { QrcodeStream } from 'vue-qrcode-reader'



const $q = useQuasar();
const $router = useRouter();
const ui = reactive({
    loading: false,
    date: null,
    time: null,
    timerID: null,
    inLoading: false,
    outLoading: false,
})
const employeeCode = ref()
const employeeCodeClass = ref()
const employeeCodeBg = ref()

onMounted(()=>{
    ui.timerID = setInterval(updateTime, 1000);
    updateTime();
    document.addEventListener('keyup', function (evt) {
        if (evt.code === 'KeyI' && evt.ctrlKey) {
            onTimeIn()
        }else if(evt.code === 'KeyO' && evt.ctrlKey){
            onTimeOut()
        }
    });
})


function updateTime() {
    var cd = new Date();
    ui.time = date.formatDate(cd, 'hh:mm:ss A')
    ui.date = date.formatDate(cd, 'MMMM D, YYYY (dddd)')
};

async function onTimeIn(){
    console.log('onTimeIn:', employeeCode.value)
    ui.inLoading = true
    try{
        const { data } = await axios.post(`/api/attendance/login`, { code: employeeCode.value })
        if( data?.action=='in_pm' ){
            $q.notify({
                message: `${data.employee.fullname} logging in for afternoon!`,
                type: 'positive',
            })
        }else{
            $q.notify({
                message: `${data.employee.fullname} logging in!`,
                type: 'positive',
            })
        }
    }catch(e){
        if(e.response.data?.error){
            $q.notify({
                message: e.response.data?.error,
                type: 'negative',
            })
        }else{
            $q.notify({
                message: `Error loggin In`,
                type: 'negative',
            })
        }
    }
    ui.inLoading = false
}

async function onTimeOut(){
    console.log('onTimeOut:', employeeCode.value)
    ui.outLoading = true
    try{
        const { data } = await axios.post(`/api/attendance/logout`, { code: employeeCode.value })
        if(data?.action=='am_out'){
            $q.notify({
                message: `${data.employee.fullname} logging out for lunch break!`,
                type: 'positive',
            })
        }else if(data?.action=='pm_out'){
            let hrs = data.attendance.hours
            $q.notify({
                message: `${data.employee.fullname} logging out, total hours: ${hrs}`,
                type: 'positive',
            })
        }else{
            $q.notify({
                message: `${data.employee.fullname} logging out!`,
                type: 'positive',
            })
        }
    }catch(e){
        console.log(e)
        if(e.response?.data?.error){
            $q.notify({
                message: e.response.data?.error,
                type: 'negative',
            })
        }else{
            $q.notify({
                message: `Error loggin Out`,
                type: 'negative',
            })
        }
    }
    ui.outLoading = false
}

async function onDetect(data){
    employeeCodeClass.value = ''
    debounce(function(){
        employeeCodeClass.value = 'animate__bounceIn'
        employeeCodeBg.value = 'accent'
    },200)()
    debounce(function(){
        employeeCodeBg.value = ''
    },1000)()
    employeeCode.value = data[0].rawValue
    try{
        const { data } = await axios.post(`/api/attendance/qrcode`, { code: employeeCode.value })
        if( data?.message ){
            $q.notify({
                message: `${data.message}`,
                type: 'positive',
            })
        }
    }catch(e){
        if(e.response.data?.error){
            $q.notify({
                message: e.response.data?.error,
                type: 'negative',
            })
        }else{
            $q.notify({
                message: `Error loggin In`,
                type: 'negative',
            })
        }
    }
}

function onError(data){
    console.log(data)
}
</script>


<style lang="scss">
    #clock {
        font-family: 'Share Tech Mono', monospace;
        color: #ffffff;
        text-align: center;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        color: #daf6ff;
        text-shadow: 0 0 20px rgba(10, 175, 230, 1),  0 0 20px rgba(10, 175, 230, 0);
        .time {
            letter-spacing: 0.05em;
            font-size: 80px;
            padding: 5px 0;
        }
        .date {
            letter-spacing: 0.1em;
            font-size: 30px;
        }
        .text {
            letter-spacing: 0.1em;
            font-size: 12px;
            padding: 20px 0 0;
        }
    }
</style>
