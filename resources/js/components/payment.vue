<template>
    <q-dialog persistent ref="dialog">
        <div style="width: 800px; max-width: 80vw;">
            <q-form @submit.prevent="onTransact">
                <q-card>
                    <q-card-section>
                        <div class="row">
                            <div class="col">
                                <h1 class="text-h4 text-weight-bold q-mt-none">Barwsa</h1>
                            </div>
                            <div class="col-5">
                                <b>Invoice #: {{ invoice.number }}</b><br />
                                <b>Date:</b> {{ invoice.date }}<br />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                Balao Rural Welfare and Service Association<br />
                                Balao, Barili, Cebu
                            </div>
                            <div class="col-5">
                                <b>To:</b>
                                {{ consumer.name }}<br />
                                <b>Period:</b> <br>{{ consumer.period }}
                            </div>
                        </div>
                    </q-card-section>
                    <q-separator/>
                    <q-card-section>
                        <q-table
                            flat
                            bordered
                            binary-state-sort
                            class="sticky-dynamic"
                            row-key="id"
                            v-model:pagination="table.pagination"
                            :rows="table.rows"
                            :columns="table.columns"
                        >
                            <template v-slot:bottom>
                                <div class="col-12 row justify-between">
                                    <div class="col"></div>
                                    <div class="col-4">
                                        <table>
                                            <tr class="text-subtitle1 text-weight-bold">
                                                <td class="text-right"><b>Subtotal:</b></td>
                                                <td>₱{{ invoice.subtotal }}</td>
                                            </tr>
                                            <!-- <tr class="text-subtitle1 text-weight-bold">
                                                <td class="text-right"><b>Tax:</b></td>
                                                <td>{{ invoice.tax }}</td>
                                            </tr> -->
                                            <tr class="text-subtitle1 text-weight-bold">
                                                <td class="text-right"><b>Total:</b></td>
                                                <td>₱{{ invoice.total }}</td>
                                            </tr>
                                            <tr class="text-subtitle1 text-weight-bold">
                                                <td class="text-right"><b>Change:</b></td>
                                                <td>₱{{ invoice.changed }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </template>
                        </q-table>
                    </q-card-section>
                    <q-separator v-if="!isCustomer" />
                    <q-card-section v-if="!isCustomer">
                        <div class="row q-gutter-md">
                            <div class="col">
                                <q-input dense outlined label="Reference #" v-model="invoice.receipt"></q-input>
                            </div>
                            <div class="col">
                                <q-input
                                    dense
                                    outlined
                                    label="Cash"
                                    v-model="invoice.cash"
                                    :rules="[val => !!val || 'Field is required']"
                                    type="number">
                                </q-input>
                            </div>
                        </div>
                    </q-card-section>
                    <q-separator/>
                    <q-card-actions align="right">
                        <q-btn flat label="Close" color="negative" v-close-popup />
                        <q-btn
                            v-if="!isCustomer"
                            flat
                            label="Transact"
                            :disable="!canTransact"
                            :class="{'bg-primary':canTransact,'bg-grey':!canTransact}"
                            type="submit"
                        />
                    </q-card-actions>
                </q-card>
            </q-form>
        </div>
    </q-dialog>
</template>


<script setup>
import { ref, reactive, onMounted, defineExpose, computed, defineEmits } from "vue";
import { useQuasar, date } from 'quasar'
import { useStore } from "vuex"


const $q = useQuasar()
const $store = useStore();
const isCustomer = $store.getters['auth/isCustomer'];
const dialog = ref()
const consumer = reactive({})
const invoice = reactive({
    id: null,
    number: '',
    date: null,
    price: 1,
    subtotal: computed(()=>{
        // return parseFloat(consumer.data.total_payable).toFixed(2)
        return consumer.data.total_payable
    }),
    tax: 0,
    total: computed(()=>{
        return invoice.subtotal
    }),
    reciept: '',
    cash: '',
    changed: computed(()=>{
        if( invoice.cash >= parseFloat(invoice.total) )
            return parseFloat(invoice.cash - invoice.total).toFixed(2);
        else
            return 0
    })
})
const table = reactive({
    loading: false,
    filter: '',
    rows: [],
    columns: [
        {
            label: "Previous",
            name: "previous",
            field: "previous",
            align: 'left',
        },
        {
            label: "Current",
            name: "current",
            field: "current",
            align: 'left',
        },
        {
            label: "CUM Used",
            name: "volume",
            field: "volume",
            align: 'left',
            format(v){
                return parseFloat(v).toFixed(2)
            }
        },
        {
            label: "Payable",
            name: "payable",
            field: "payable",
            align: 'left',
            format: (v,r) => '₱'+parseFloat(v).toFixed(2)
        },
    ],
    pagination: {
        sortBy: "id",
        descending: true,
        page: 1,
        rowsPerPage: 10,
        rowsNumber: 10,
    }
})
const ui = reactive({
    loading: false
})
const $emit = defineEmits([
    'payment:error',
    'payment:success'
])

const canTransact = computed(()=>{
    return invoice.cash >= invoice.total
})



onMounted(()=>{
    invoice.date = date.formatDate(Date.now(), 'MMMM DD, YYYY')
})

function show(row){
    let data = _.cloneDeep(row)
    dialog.value.show()
    // clear previous data
    invoice.cash = ''
    consumer.data = data
    consumer.name = `${data.user.first_name} ${data.user.last_name} `
    consumer.period = `${data.consumption_dates.from} - ${data.consumption_dates.to} `
    table.rows = data.consumptions
    invoice.number = 'INV-' + date.formatDate(Date.now(), 'YYYYMMDD') + `${data.id}`
    invoice.id = data.id
}

async function onTransact(){
    $q.loading.show()
    let params = {
        ...invoice,
        consumptions: _.map(table.rows, 'id')
    }
    try{
        const { data } = await axios.post(`api/billing`, params)
        if( data ){
            dialog.value.hide()
            $q.notify({
                type: 'positive',
                message: 'Payment success!'
            })
            $emit("payment:success", data)
        }
    }
    catch(e){
        console.log(e)
        $emit("payment:error")
        $q.notify({
            type: 'negative',
            message: 'Error occured!'
        })
    }
    $q.loading.hide()
}

defineExpose({
    show
})
</script>


<style lang="sass">
.sticky-dynamic
  /* height or max-height is important */
  height: 320px

  .q-table__top,
  .q-table__bottom,
  thead tr:first-child th /* bg color is important for th; just specify one */
    background-color: #ffffff

  thead tr th
    position: sticky
    z-index: 1
  /* this will be the loading indicator */
  thead tr:last-child th
    /* height of all previous header rows */
    top: 48px
  thead tr:first-child th
    top: 0

  /* prevent scrolling behind sticky top row on focus */
  tbody
    /* height of all previous header rows */
    scroll-margin-top: 48px
</style>

<style>
.scroll{overflow-x:auto;max-height:500px;border:none;border-radius:8px}
.scroll::-webkit-scrollbar{width:10px;height:10px}
.scroll::-webkit-scrollbar-track{background-color:#f5f5f5;border-radius:10px}
.scroll::-webkit-scrollbar-track{background-color:#f5f5f5;border-radius:10px}
.scroll::-webkit-scrollbar-thumb{background-color:var(--q-primary);border-radius:10px}
.scroll::-webkit-scrollbar-corner{background-color:#f5f5f5}
</style>
