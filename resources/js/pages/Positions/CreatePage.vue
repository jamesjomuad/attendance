<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onCreate">
            <div class="col-md-12">
                <q-card>
                    <!-- Fields -->
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <q-input
                                v-model="$form.title"
                                dense
                                outlined
                                label="Title"
                                class="col-12"
                                name="title"
                                :rules="[ val => val && val.length > 0 || 'Please type something']"
                            >
                                <template v-slot:prepend>
                                <q-icon name="account_circle" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.description"
                                dense
                                outlined
                                label="Description"
                                class="col-12"
                                type="textarea"
                            ></q-input>
                        </div>
                    </q-card-section>
                </q-card>
                <div class="row justify-end q-mt-md">
                    <div class="col-auto">
                        <q-btn
                            color="primary"
                            type="submit"
                            label="Save"
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
import { ref, reactive, } from "vue";
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
})
const $form = reactive({
    title: "",
    description: "",
},);


async function onCreate(){
    ui.loading = true
    try{
        const { data } = await axios.post(`/api/positions`, $form)
        $q.notify({
            type: 'positive',
            message: `Created successfully!`
        })
        $router.push('/positions')
    }catch(error){
        console.log(error)
        $q.notify({
            type: 'negative',
            message: "Error!"
        })
    }
    ui.loading = false
}

</script>
