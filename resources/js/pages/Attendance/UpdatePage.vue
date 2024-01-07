<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onUpdate">

        </q-form>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import { useQuasar, date } from 'quasar'
import _ from 'lodash'


const $q = useQuasar()
const ui = reactive({
    loading: false,
})
const $form = ref({
    in_am: "",
    out_am: "",
    in_pm: "",
    out_pm: "",
});


onMounted(async ()=>{
    ui.loading = true
    const { data } = await axios.get(`/api/attendances/${$route.params.id}`)
    $form.value = {...$form.value, ...data}
    $form.value.first_name = data?.user.first_name
    $form.value.last_name = data?.user.last_name
    $form.value.schedule_in = date.formatDate($form.value.schedule_in, 'hh:mm A')
    $form.value.schedule_out = date.formatDate($form.value.schedule_out, 'hh:mm A')
    ui.loading = false
})


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

</script>

