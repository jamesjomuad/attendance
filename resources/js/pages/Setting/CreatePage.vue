<template>
    <q-page padding>
        <q-form @submit.prevent.stop="onSave">
            <q-card>
                <!-- Title -->
                <q-card-section class="q-py-sm">
                    <div class="text-h6">Role</div>
                </q-card-section>
                <!-- Fields -->
                <q-card-section>
                    <div class="row q-col-gutter-md">
                        <q-input
                            v-model="$form.name"
                            dense
                            outlined
                            label="Name"
                            class="col-6"
                            :rules="[ val => val && val.length > 0 || 'Please type something']"
                        ></q-input>
                        <q-input
                            v-model="$form.code"
                            dense
                            outlined
                            label="Code"
                            class="col-6"
                        ></q-input>
                        <q-input
                            v-model="$form.description"
                            dense
                            outlined
                            label="Description"
                            lazy-rules
                            class="col-12"
                            type="textarea"
                        ></q-input>
                    </div>
                </q-card-section>
                <q-separator />
                <q-card-actions align="right">
                    <q-btn
                        color="primary"
                        type="submit"
                        :disable="ui.loading"
                        :loading="ui.loading"
                    >Save</q-btn>
                </q-card-actions>
            </q-card>
        </q-form>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'


const $router = useRouter();
const $q = useQuasar()
const $form = reactive({})
const ui = reactive({
    loading: false
})

watch(() => _.cloneDeep($form.name), (n,o)=>{
    $form.code = $form.name.replace(/\s+/g, '-').toLowerCase();
})

async function onSave(){
    ui.loading = true
    try {
        const { data } = await axios.post(`/api/roles`, $form)
        $q.notify({
            type: 'positive',
            message: `${data.message}`
        })
        $router.push('/system/roles')
    }
    catch (error) {
        console.log(error)
        $q.notify({
            type: 'negative',
            message: "Error!"
        })
    }
    ui.loading = false
}
</script>
