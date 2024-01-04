<template>
    <q-page padding>
        <!-- Table -->
        <div class="col-auto">
            <q-table
                :dark="$q.dark.isActive"
                flat
                bordered
                binary-state-sort
                title="Consumptions"
                row-key="id"
                v-model:pagination="table.pagination"
                :rows="table.rows"
                :columns="table.columns"
                :loading="table.loading"
                :filter="table.filter"
                :rows-per-page-options="[20, 40, 60, 80, 100, 150, 200, 250, 300]"
                @request="onRequest"
            >
                <template v-slot:top-right="props">
                    <q-input
                        outlined
                        dense
                        ref="search"
                        debounce="300"
                        v-model="table.filter"
                        placeholder="Search"
                        class="q-ma-xs"
                    >
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                    <q-btn
                        round
                        size="md"
                        color="primary"
                        class="q-ml-sm"
                        icon="add"
                        @click="onCreate">
                    </q-btn>
                    <q-btn
                        round
                        size="md"
                        color="info"
                        class="q-ml-sm"
                        icon="refresh"
                        @click="onRefresh">
                    </q-btn>
                    <q-btn
                        flat
                        round
                        size="md"
                        class="q-ml-sm"
                        color="grey-5"
                        :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                        @click="props.toggleFullscreen"
                    >
                        <q-tooltip>Toggle Fullscreen</q-tooltip>
                    </q-btn>
                </template>
                <template #body-cell-invoice="props">
                    <q-td :props="props">
                        <div class="q-gutter-md" style="font-size: 2em">
                            <q-icon v-if="props.row.xero_invoice_id" name="check_circle" color="positive"/>
                            <q-icon v-else name="warning" color="negative"/>
                        </div>
                    </q-td>
                </template>
                <template #body-cell-payment="props">
                    <q-td :props="props">
                        <div class="q-gutter-md" style="font-size: 2em">
                            <q-icon v-if="props.row.payment" name="check_circle" color="positive"/>
                            <q-icon v-else name="warning" color="negative"/>
                        </div>
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
                <template v-slot:loading>
                    <q-inner-loading showing color="primary" />
                </template>
            </q-table>
        </div>

        <!-- Select Customer -->
        <q-dialog ref="dialog">
            <q-card style=" width: 500px; ">
                <q-card-section>
                    <div class="text-h6">Select Consumer</div>
                </q-card-section>
                <q-separator/>
                <q-card-section>
                    <q-select
                        outlined
                        dense
                        map-options
                        use-input
                        input-debounce="300"
                        clearable
                        label="Find Consumer"
                        v-model="ui.client_id"
                        :options="ui.clients"
                        :loading="ui.loading"
                        @update:model-value="onConsumerSelect"
                        @filter="filterFn"
                    />
                </q-card-section>
            </q-card>
        </q-dialog>
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'


const $router = useRouter();
const $q = useQuasar()
const dialog = ref()
const table = reactive({
    loading: false,
    filter: '',
    rows: [],
    columns: [
        {
            label: "#",
            name: "id",
            field: "id",
            sortable: true,
        },
        {
            label: "Name",
            name: "consumer",
            field: "consumer",
            format: (val, row) => val?.user?.fullname,
            align: 'left',
            sortable: false,
        },
        {
            label: "Volume",
            name: "volume",
            field: "volume",
            sortable: false,
        },
        {
            label: "Unit",
            name: "unit",
            field: "unit_formatted",
            sortable: false,
        },
        {
            label: "Cost",
            name: "payable",
            field: "payable",
            sortable: false,
        },
        {
            label: "Paid",
            name: "is_paid",
            field: "is_paid",
            sortable: false,
        },
        {
            label: 'Created At',
            field: 'created_at',
            sortable: true,
            align: 'left',
            format: (val, row) => {
                let time = moment(val)
                return time.format("YYYY-MM-d HH:mm A") + ' (' + time.fromNow() + ')'
            },
        }
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
    loading:false ,
    clientsStore: [],
    clients: [],
    client_id: [],
})


onMounted(() => {
    onRequest({
        pagination: table.pagination,
        filter: null,
    });
});


  //  Server Request
async function onRequest(props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination;
    const filter = props.filter; //  Search bar value

    table.loading = true;

    // get all rows if "All" (0) is selected
    const fetchCount = (rowsPerPage === 0) ? table.pagination.rowsNumber : rowsPerPage;

    const params = {
        filter: filter,
        limit: fetchCount == 1 ? -1 : fetchCount,
        sortBy: sortBy,
        orderBy: descending ? "desc" : "asc",
        page: page,
        per_page: rowsPerPage
    };

    try {
        const { data } = await axios.get(`/api/consumption`, {params})

        table.pagination.rowsNumber = data.total;

        // clear out existing data and add new
        table.rows.splice(0, table.rows.length, ...data.data);

        // don't forget to update local pagination object
        table.pagination.page = page;
        table.pagination.rowsPerPage = rowsPerPage;
        table.pagination.sortBy = sortBy;
        table.pagination.descending = descending;
    }
    catch (error) {
        $q.notify({
            color: 'negative',
            message: error.response.statusText,
            icon: 'report_problem',
            position: 'bottom-right'
        })
        table.loading = false;
    }

    // ...and turn of loading indicator
    table.loading = false;
}

function onRow(evt, row, index){
    $router.push(`/consumers/${row.id}`)
}

function onRefresh(){
    onRequest({
        pagination: table.pagination,
        filter: null,
    });
}

async function onCreate(){
    ui.loading = true
    dialog.value.show()
    const { data } = await axios.get(`/api/consumers`)
    ui.clientsStore = _.map(data.data, (v)=>{
        return {
            label: `${v.first_name} ${v.last_name}`,
            value: v.id,
            data: v
        }
    })
    ui.loading = false
}

async function filterFn (val, update, abort) {
    if (val === '') {
        update(() => {
            ui.clients = ui.clientsStore
        })
        return
    }

    if (val.length < 3) {
        abort()
        return
    }

    const { data } = await axios.get(`/api/consumers?filter=${val}`)

    update(() => {
        ui.clients = _.map(data.data, (v)=>{
            return {
                label: `${v.first_name} ${v.last_name}`,
                value: v.id,
                data: v
            }
        })
    })

}

function onConsumerSelect(v){
    $router.push(`/consumptions/create/${v.value}`)
}
</script>
