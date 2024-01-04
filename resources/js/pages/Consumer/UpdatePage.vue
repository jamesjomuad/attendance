<!-- eslint-disable brace-style -->
<template>
    <q-page padding>
        <q-form class="row q-col-gutter-md" @submit="onUpdate">
            <div class="col-md-12">
                <q-card>
                    <!-- Title -->
                    <q-card-section class="q-py-sm">
                        <div class="text-h6">
                        Account
                        </div>
                    </q-card-section>
                    <!-- Fields -->
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                            <q-input
                                dense
                                outlined
                                v-model="$form.name"
                                label="Username"
                                class="col-12"
                                readonly
                            >
                                <template v-slot:prepend>
                                    <q-icon name="perm_identity" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.first_name"
                                dense
                                outlined
                                label="First Name"
                                class="col-6"
                                :rules="[ val => val && val.length > 0 || 'Please type something']"
                            >
                                <template v-slot:prepend>
                                <q-icon name="account_circle" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.last_name"
                                dense
                                outlined
                                label="Last name *"
                                lazy-rules
                                class="col-6"
                                :rules="[ val => val && val.length > 0 || 'Please type something']"
                            >
                                <template v-slot:prepend>
                                <q-icon name="account_circle" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.consumer.phone"
                                dense
                                outlined
                                label="Phone"
                                lazy-rules
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                <q-icon name="phone" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.consumer.phone_2"
                                dense
                                outlined
                                label="Work Phone"
                                lazy-rules
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                <q-icon name="phone" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.email"
                                dense
                                outlined
                                label="Email"
                                lazy-rules
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                <q-icon name="email" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.consumer.dob"
                                dense
                                outlined
                                label="Date of Birth"
                                lazy-rules
                                class="col-6"
                                readonly
                            >
                                <template v-slot:prepend>
                                <q-icon name="calendar_month" />
                                </template>
                                <template v-slot:after>
                                    <q-btn icon="event" round color="primary">
                                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                            <q-date v-model="$form.consumer.dob">
                                            <div class="row items-center justify-end q-gutter-sm">
                                                <q-btn label="Cancel" color="primary" flat v-close-popup />
                                                <q-btn label="OK" color="primary" flat v-close-popup />
                                            </div>
                                            </q-date>
                                        </q-popup-proxy>
                                    </q-btn>
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.consumer.barangay"
                                dense
                                outlined
                                label="Barangay"
                                lazy-rules
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="place" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.consumer.sitio"
                                dense
                                outlined
                                label="Sitio"
                                lazy-rules
                                class="col-6"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="place" />
                                </template>
                            </q-input>

                            <q-input
                                v-model="$form.consumer.billing_address"
                                dense
                                outlined
                                label="Billing Address"
                                lazy-rules
                                class="col-12"
                            >
                                <template v-slot:prepend>
                                <q-icon name="home" />
                                </template>
                            </q-input>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <div class="row q-col-gutter-md">
                        <q-input
                            v-model="$form.consumer.meter_id"
                            dense
                            outlined
                            label="Meter ID"
                            lazy-rules
                            class="col-12"
                        >
                        </q-input>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-actions align="right">
                        <div class="row q-gutter-md">
                            <q-btn
                                label="Remove"
                                color="negative"
                                :disable="ui.removing"
                                :loading="ui.removing"
                                @click="onRemove"
                            />
                            <q-btn
                                label="Reset Password"
                                color="warning"
                                :disable="ui.resetting"
                                :loading="ui.resetting"
                                @click="onResetPassword"
                            />
                            <q-btn
                                label="Update"
                                color="primary"
                                :disable="ui.updating"
                                :loading="ui.updating"
                                type="submit"
                            />
                        </div>
                    </q-card-actions>
                    <q-inner-loading
                        :showing="ui.loading"
                        label="Please wait..."
                        label-style="font-size: 1.1em"
                    />
                </q-card>
            </div>
        </q-form>

        <q-table
            :dark="$q.dark.isActive"
            flat
            bordered
            binary-state-sort
            class="q-mt-md"
            title="Consumptions"
            row-key="id"
            selection="multiple"
            v-model:pagination="table.pagination"
            v-model:selected="table.selected"
            :rows="table.rows"
            :columns="table.columns"
            :loading="table.loading"
            :filter="table.filter"
            :rows-per-page-options="[20, 40, 60, 80, 100, 150, 200, 250, 300]"
        >
            <template #body-cell-previous="props">
                <q-td :props="props">
                    <q-input
                        dense
                        outlined
                        type="number"
                        v-if="props.row.previousEdit"
                        v-model="props.row.previous"
                        @blur="props.row.previousEdit = false"
                        @keyup.enter="props.row.previousEdit = false"
                    />
                    <span
                        v-else
                        @click="props.row.previousEdit = true;">
                        {{ props.row.previous }}
                    </span>
                </q-td>
            </template>
            <template #body-cell-current="props">
                <q-td :props="props">
                    <q-input
                        dense
                        outlined
                        type="number"
                        v-if="props.row.currentEdit"
                        v-model="props.row.current"
                        @blur="props.row.currentEdit = false"
                        @keyup.enter="props.row.currentEdit = false"
                    />
                    <span v-else  @click="props.row.currentEdit = true">{{ props.row.current }}</span>
                </q-td>
            </template>
            <template #body-cell-volume="props">
                <q-td :props="props">
                    <span v-if="!props.row.id">{{ props.row.current - props.row.previous }}</span>
                    <span v-else>{{ props.row.volume }}</span>
                </q-td>
            </template>
            <template #body-cell-is_paid="props">
                <q-td :props="props">
                    <div class="q-gutter-md" style="font-size: 2em">
                        <q-icon v-if="props.row.is_paid" name="check_circle" color="positive"/>
                        <q-icon v-else name="warning" color="negative"/>
                    </div>
                </q-td>
            </template>
            <template v-slot:bottom>
            </template>
        </q-table>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import _ from 'lodash'


const $route = useRoute()
const $router = useRouter()
const $q = useQuasar()
const ui = reactive({
    loading: false,
    updating: false,
    resetting: false,
    removing: false
})
const $form = ref({
    first_name: "",
    last_name: "",
    email: "",
    consumer: {}
});
const table = reactive({
    loading: false,
    filter: '',
    rows: [],
    columns: [
        {
            label: "#",
            name: "id",
            field: "id",
            align: 'left',
            sortable: false,
        },
        {
            label: "Previous",
            name: "previous",
            field: "previous",
            align: 'left',
            sortable: false,
        },
        {
            label: "Current",
            name: "current",
            field: "current",
            align: 'left',
            sortable: false,
        },
        {
            label: "Volume",
            name: "volume",
            field: "volume",
            align: 'left',
            sortable: false,
        },
        {
            label: "Is Paid",
            name: "is_paid",
            field: "is_paid",
            align: 'left',
        },
        {
            label: 'Created',
            field: 'created_at',
            sortable: false,
            align: 'left',
            format: (val, row) => {
                // return moment(val).format("MMMM DD, YYYY (h:mm a)");
                return moment(val).format("YYYY-MM-DD");
            },
        },
        {
            label: 'Updated',
            field: 'updated_at',
            sortable: false,
            align: 'left',
            format: (val, row) => {
                return moment(val).format("YYYY-MM-DD");
            }
        },
    ],
    pagination: {
        sortBy: "id",
        descending: true,
        page: 1,
        rowsPerPage: 10,
        rowsNumber: 10,
    },
    selected: []
})


onMounted(async ()=>{
    ui.loading = true
    const { data } = await axios.get(`/api/consumers/${$route.params.id}`)
    $form.value = {...$form.value, ...data}
    table.rows = data.consumptions
    ui.loading = false
})


async function onUpdate(){
    ui.loading = true
    ui.updating = true
    try{
        const { data } = await axios.put(`/api/consumers/${$route.params.id}`, $form.value)
        if(data.status){
            $q.notify({
                type: 'positive',
                message: `Updated successfully!`
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
            const { data } = await axios.post(`/api/consumers/${$route.params.id}`, {
                _method: 'delete'
            })
            if(data){
                $router.push('/consumers')
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
            const { data } = await axios.put(`/api/consumers/${$route.params.id}`, {
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

