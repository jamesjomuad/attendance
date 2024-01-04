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
    </q-page>
</template>


<script setup>
import { ref, reactive, onMounted } from "vue";
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'


const $router = useRouter();
const $q = useQuasar()
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
            name: "fullname",
            field: "fullname",
            sortable: false,
            align: 'left'
        },
        {
            label: "Invoice #",
            name: "number",
            field: "number",
            sortable: false,
            align: 'left'
        },
        {
            label: "Volume",
            name: "total_volume",
            field: "total_volume",
            sortable: false,
            align: 'left'
        },
        {
            label: "Total",
            name: "total",
            field: "total",
            sortable: false,
            align: 'left'
        },
        {
            label: "Cash",
            name: "cash",
            field: "cash",
            sortable: true,
            align: 'left'
        },
        {
            label: "Paid",
            name: "is_paid",
            field: "is_paid",
            sortable: true,
            align: 'left'
        },
        {
            label: "Reference",
            name: "reference",
            field: "reference",
            sortable: true,
            align: 'left'
        },
        {
            label: 'Created At',
            field: 'created_at',
            sortable: true,
            align: 'left',
            format: (val, row) => {
                // return moment(val).format("MMMM DD, YYYY (h:mm a)");
                return moment(val).format("YYYY-MM-d");
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
        const { data } = await axios.get(`/api/transactions`, {params})

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
    $router.push(`/system/users/${row.id}`)
}

function onRefresh(){
    onRequest({
        pagination:table.pagination,
        filter: null,
    });
}
</script>
