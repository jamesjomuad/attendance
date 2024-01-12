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
                        rounded
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
                <template #body-cell-in_am="props">
                    <q-td :props="props">
                        <q-badge v-if="props.row.is_late" color="red">Late</q-badge>
                        {{ props.row.in_am }}
                    </q-td>
                </template>
                <template #body-cell-action="props">
                    <q-td :props="props">
                        <div class="row justify-end q-gutter-sm">
                            <q-btn round size="sm" color="primary" icon="edit" @click="onRow(props.row)"/>
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
import { useQuasar, useMeta } from 'quasar'
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
            label: "Employee ID",
            name: "employee_code",
            field: "employee_code",
            align: 'left',
            sortable: false,
        },
        {
            label: "Name",
            name: "fullname",
            field: "fullname",
            align: 'left',
            sortable: false,
        },
        {
            label: "Time In AM",
            name: "in_am",
            field: "in_am",
            sortable: false,
        },
        {
            label: "Time Out AM",
            name: "out_am",
            field: "out_am",
            align: 'left',
            sortable: false,
        },
        {
            label: "Time In PM",
            name: "in_pm",
            field: "in_pm",
            sortable: false,
        },
        {
            label: "Time Out PM",
            name: "out_pm",
            field: "out_pm",
            align: 'left',
            sortable: false,
        },
        {
            label: 'Total Hours',
            field: 'total_hours',
            sortable: false,
            align: 'left',
        },
        {
            label: 'Date',
            field: 'created_at',
            sortable: true,
            align: 'left',
            format: (val, row) => {
                return moment(val).format("YYYY-MM-DD");
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

useMeta({
    title: 'Attendance',
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
        const { data } = await axios.get(`/api/attendances`, {params})

        table.pagination.rowsNumber = data.total;

        // clear out existing data and add new
        table.rows.splice(0, table.rows.length, ...data.data);

        // don't forget to update local pagination object
        table.pagination.page = page;
        table.pagination.rowsPerPage = rowsPerPage;
        table.pagination.sortBy = sortBy;
        table.pagination.descending = descending;
    } catch (error) {
        $q.notify({
            color: 'negative',
            message: error.response?.statusText,
            icon: 'report_problem',
            position: 'bottom-right'
        })
        table.loading = false;
    }

    // ...and turn of loading indicator
    table.loading = false;
}

function onRow(row){
    $router.push(`/attendance/${row.id}`)
}

function onRefresh(){
    onRequest({
        pagination: table.pagination,
        filter: null,
    });
}
</script>
