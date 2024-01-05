<template>
    <q-page padding>
        <!-- Table -->
        <div class="col-auto">
            <q-table
                :dark="$q.dark.isActive"
                flat
                bordered
                binary-state-sort
                title="Users"
                row-key="id"
                v-model:pagination="table.pagination"
                :rows="table.rows"
                :columns="table.columns"
                :loading="table.loading"
                :filter="table.filter"
                :rows-per-page-options="[20, 40, 60, 80, 100, 150, 200, 250, 300]"
                @request="onRequest"
                @row-click="onRow"
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
                        size="md"
                        color="primary"
                        class="q-ml-sm"
                        icon="add"
                        to="/system/users/create">
                    </q-btn>
                    <q-btn
                        size="md"
                        color="secondary"
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
                            <q-btn-dropdown
                                flat
                                rounded
                                color="primary"
                                dropdown-icon="more_vert"
                                class="card-action"
                            >
                                <q-list>
                                    <q-item clickable @click="onView(props)">
                                        <q-item-section>
                                            <q-item-label>View</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                    <q-item v-if="!props.row.xero_invoice_id" clickable>
                                        <q-item-section>
                                            <q-item-label>Generate Invoice</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                    <q-item clickable>
                                        <q-item-section>
                                            <q-item-label>Resend Email</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                    <q-item clickable @click="mailTo(props.row)">
                                        <q-item-section>
                                            <q-item-label>Email</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </q-btn-dropdown>
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
            label: "Username",
            name: "username",
            field: "username",
            sortable: true,
            align: 'left'
        },
        {
            label: "First name",
            name: "first_name",
            field: "first_name",
            sortable: true,
            align: 'left'
        },
        {
            label: "Last name",
            name: "last_name",
            field: "last_name",
            sortable: true,
            align: 'left'
        },
        {
            label: "Email",
            name: "email",
            field: "email",
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
        const { data } = await axios.get(`/api/users`, {params})

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
        pagination: table.pagination,
        filter: null,
    });
}
</script>
