<template>
    <q-page class="flex flex-center bg-dark">
        <div id="clock" class="column">
            <p class="col date">{{ ui.date }}</p>
            <p class="col time">{{ ui.time }}</p>
            <div class="row">
                <div class="col">
                    <q-btn color="primary" icon="login" label="Time In" @click="onTimeIn"/>
                </div>
                <div class="col">
                    <q-btn color="secondary" icon-right="logout" label="Time Out" @click="onTimeOut"/>
                </div>
            </div>
        </div>
    </q-page>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useQuasar, date } from "quasar";
import { useRouter } from "vue-router";



const $q = useQuasar();
const $router = useRouter();
const isPwd = ref(true);
const ui = reactive({
    loading: false,
    date: null,
    time: null
})

onMounted(()=>{
    var timerID = setInterval(updateTime, 1000);
    updateTime();
})


function updateTime() {
    var cd = new Date();
    ui.time = date.formatDate(cd, 'hh:mm:ss A')
    ui.date = date.formatDate(cd, 'MMMM MM, YYYY (dddd)')
};

function zeroPadding(num, digit) {
    var zero = '';
    for(var i = 0; i < digit; i++) {
        zero += '0';
    }
    return (zero + num).slice(-digit);
}

function onTimeIn(){}
function onTimeOut(){}

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
            font-size: 24px;
        }
        .text {
            letter-spacing: 0.1em;
            font-size: 12px;
            padding: 20px 0 0;
        }
    }
</style>
