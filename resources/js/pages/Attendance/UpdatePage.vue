<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onUpdate">
            <div class="col-md-12">
                <q-card class="q-mt-md">
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <!-- In  AM-->
                            <q-input
                                dense
                                outlined
                                label="Time In AM"
                                class="col-6"
                                v-model="$form.in_am">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.in_am">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                            <!-- Out AM -->
                            <q-input
                                dense
                                outlined
                                label="Time Out AM"
                                class="col-6"
                                v-model="$form.out_am">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.out_am">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                            <!-- In  PM-->
                            <q-input
                                dense
                                outlined
                                label="Time In PM"
                                class="col-6"
                                v-model="$form.in_pm">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.in_pm">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                            <!-- Out PM -->
                            <q-input
                                dense
                                outlined
                                label="Time Out PM"
                                class="col-6"
                                v-model="$form.out_pm">
                                <template v-slot:append>
                                <q-icon name="access_time" class="cursor-pointer">
                                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                    <q-time v-model="$form.out_pm">
                                        <div class="row items-center justify-end">
                                        <q-btn v-close-popup label="Close" color="primary" flat />
                                        </div>
                                    </q-time>
                                    </q-popup-proxy>
                                </q-icon>
                                </template>
                            </q-input>
                        </div>
                    </q-card-section>
                </q-card>

                <!-- Action -->
                <div class="row justify-end q-mt-md">
                    <div class="col-auto">
                        <q-btn
                            color="negative"
                            label="Remove"
                            :disable="ui.removing"
                            :loading="ui.removing"
                            @click="onRemove"
                        />
                        <q-btn
                            color="primary"
                            type="submit"
                            label="Save"
                            class="q-ml-md"
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
import { useQuasar, date } from 'quasar'
import _ from 'lodash'
import { useRoute } from 'vue-router'


const $route = useRoute();
const $q = useQuasar()
const ui = reactive({
    loading: false,
})
const $form = reactive({
    in_am: "",
    out_am: "",
    in_pm: "",
    out_pm: "",
});


onMounted(async ()=>{
    ui.loading = true
    const { data } = await axios.get(`/api/attendances/${$route.params.id}`)
    $form.in_am = data.in_am
    $form.out_am = data.out_am
    $form.in_pm = data.in_pm
    $form.out_pm = data.out_pm
    ui.loading = false

    console.log($form)
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

