<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onUpdate">

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
    const { data } = await axios.get(`/api/attendances/${$route.params.id}`)
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

