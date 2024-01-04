<template>
    <q-page padding>
        <!-- Table -->
        <div class="col-auto">
            <q-table
                :dark="$q.dark.isActive"
                flat
                bordered
                binary-state-sort
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
                        v-if="!isCustomer"
                    >
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
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
                <template #body-cell-action="props">
                    <q-td :props="props">
                        <div class="row justify-end q-gutter-sm">
                            <template v-if="!isCustomer">
                                <q-btn v-if="props.row.total_payable" size="sm" color="primary" label="Pay" @click="onPay(props)"/>
                                <q-btn size="sm" color="warning" label="Update" :to="'/billing/'+props.row?.user.id"/>
                            </template>
                            <q-btn v-if="isCustomer" size="sm" color="primary" label="Bill" @click="onViewBill(props)"/>
                        </div>
                    </q-td>
                </template>
                <template v-slot:loading>
                    <q-inner-loading showing color="primary" />
                </template>
            </q-table>
        </div>
    </q-page>

    <payment ref="terminal" @payment:success="onRefresh"/>
</template>


<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import { useStore } from "vuex"
import payment from '../../components/payment.vue'



const $router = useRouter();
const $store = useStore();
const $q = useQuasar()
const terminal = ref()
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
            sortable: true,
        },
        {
            label: "First name",
            name: "first_name",
            field: "user",
            align: 'left',
            sortable: true,
            format: (v, row) => {
                return `${v?.first_name ?? 'deleted'}`
            }
        },
        {
            label: "Last Name",
            name: "last_name",
            field: "user",
            align: 'left',
            sortable: true,
            format: (v, row) => {
                return `${v?.last_name ?? 'deleted'}`
            }
        },
        {
            label: "Consumptions",
            name: "consumptions",
            field: "total_volume",
            align: 'left',
        },
        {
            label: "Date Range",
            name: "range",
            field: "consumption_dates",
            align: 'left',
            format: (v, row) => {
                return `${v.from} - ${v.to}`
            }
        },
        {
            label: "Payable",
            name: "payable",
            field: "total_payable",
            align: 'left',
            format: (v) => {
                return `₱${v}`
            }
        },
        {
            label: "Action",
            name: "action",
            align: 'right'
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
const isCustomer = $store.getters['auth/isCustomer'];


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
        per_page: rowsPerPage,
    };

    try {
        const { data } = await axios.get(`/api/billing`, {params})

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

function onRefresh(){
    onRequest({
        pagination: table.pagination,
        filter: null,
    });
}

function onPay(props){
    terminal.value.show(props.row)
}

function onViewBill(props){
    terminal.value.show(props.row)
}

</script>
