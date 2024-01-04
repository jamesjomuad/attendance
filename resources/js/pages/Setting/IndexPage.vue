<template>
    <q-page padding>
        <q-card>
            <q-splitter v-model="splitterModel" style="height: 250px">
                <template v-slot:before>
                    <q-tabs align="left" vertical v-model="tab">
                        <q-tab name="rate" icon="trending_up" label="Rate" />
                        <q-tab name="billing-cycle" icon="autorenew" label="Billing" />
                    </q-tabs>
                </template>
                <template v-slot:after>
                    <q-tab-panels
                        v-model="tab"
                        animated
                        swipeable
                        vertical
                        transition-prev="jump-up"
                        transition-next="jump-up"
                    >
                        <q-tab-panel name="rate" style=" min-height: 240px; ">
                            <div class="text-h6 q-mb-md">Amount Per Cubic</div>
                            <div class="row q-col-gutter-md">
                                <q-input
                                    v-model="$form.volume_rate"
                                    dense
                                    outlined
                                    label="Rate"
                                    name="rate"
                                    class="col-3"
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="currency_ruble" />
                                    </template>
                                </q-input>
                                <q-select
                                    dense
                                    outlined
                                    label="Unit"
                                    name="unit"
                                    class="col-3"
                                    emit-value
                                    map-options
                                    v-model="$form.volume_unit"
                                    :options="ui.volume.units"
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="water_drop" />
                                    </template>
                                </q-select>
                            </div>
                        </q-tab-panel>

                        <q-tab-panel name="billing-cycle">
                            <div class="text-h6 q-mb-md">Billing Cycle</div>
                            <div class="row q-col-gutter-md">
                                <q-select
                                    dense
                                    outlined
                                    label="Cycle"
                                    class="col-3"
                                    emit-value
                                    map-options
                                    v-model="$form.billing_cycle"
                                    :options="ui.billing.cycles"
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="water_drop" />
                                    </template>
                                </q-select>
                                <q-select
                                    dense
                                    outlined
                                    label="Day"
                                    class="col-3"
                                    emit-value
                                    map-options
                                    v-model="$form.billing_day"
                                    :options="ui.billing.days"
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="water_drop" />
                                    </template>
                                </q-select>
                            </div>
                        </q-tab-panel>
                    </q-tab-panels>
                </template>
            </q-splitter>
            <q-separator />
            <q-card-actions align="right">
                <q-btn color="primary" @click="onSave">Save</q-btn>
            </q-card-actions>
            <q-inner-loading
                :showing="ui.loading"
                label="Please wait..."
                label-style="font-size: 1.1em"
            />
        </q-card>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import { debounce, useQuasar } from 'quasar'
import { useRouter } from 'vue-router'


const $router = useRouter();
const $q = useQuasar()
const ui = reactive({
    loading:true ,
    volume: {
        units: [
            { label: "Select", value:""},
            { label: "Milliliter (mL or ml)", value:"ml"},
            { label: "Liter (L or l)", value:"l"},
            { label: "Cubic Meter (m³)", value:"m³"},
            { label: "Cubic Inch (in³)", value:"in³"},
            { label: "Cubic Foot (ft³)", value:"ft³"},
            { label: "Gallon (gal)", value:"gal"},
        ]
    },
    billing:{
        cycles: [
            { label: "Weekly", value:"weekly"},
            { label: "Bi Monthly", value:"bi-monthly"},
            { label: "Monthly", value:"monthly"},
            { label: "Annually", value:"annually"},
            { label: "Yearly", value:"yearly"},
        ],
        days: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]
    }
})
const tab = ref('rate')
const splitterModel = ref(10)
const $form = reactive({
    volume_rate: null,
    volume_unit: ui.volume.units[0].value,
    billing_cycle: ui.billing.cycles[2].value,
    billing_day: 1
})

onMounted(async ()=>{
    let data = await request()
    data     = _.mapKeys(data, 'key')
    Object.keys(data).forEach(function(key) {
        $form[key] = data[key].value
    });
    ui.loading = false
})


async function request(){
    try{
        const { data } = await axios.get(`/api/settings`)
        return data.data
    }
    catch(error){
        console.log(error)
        $q.notify({
            type: 'negative',
            message: error.response.data.error
        })
    }
}

async function onSave(){
    ui.loading = true
    try{
        const { data } = await axios.post(`/api/settings`, $form)
        if(data.status){
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
            message: error.response.data.error
        })
    }
    ui.loading = false
}
</script>
