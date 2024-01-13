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
                @row-click="onRow"
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
                        color="primary"
                        class="q-ml-sm"
                        icon="add"
                        to="/leaves/create">
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
import _ from 'lodash'

useMeta({
    title: 'leaves',
})

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
            label: 'request_id',
            name: 'request_id',
            field: 'request_id',
            align: 'left',
            sortable: false
        },
        {
            label: 'Name',
            field: 'employee',
            align: 'left',
            sortable: false,
            format: (v) => v?.fullname
        },
        {
            label: 'type',
            name: 'type',
            field: 'type',
            align: 'left',
            sortable: false
        },
        {
            label: 'start',
            name: 'start',
            field: 'start',
            align: 'left',
            sortable: false
        },
        {
            label: 'end',
            name: 'end',
            field: 'end',
            align: 'left',
            sortable: false
        },
        {
            label: 'reason',
            name: 'reason',
            field: 'reason',
            align: 'left',
            sortable: false
        },
        // {
        //     label: 'status',
        //     name: 'status',
        //     field: 'status',
        //     align: 'left',
        //     sortable: false
        // },
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
        const { data } = await axios.get(`/api/leaves`, {params})

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

function onRow(evt, row, index){
    $router.push(`/leaves/${row.id}`)
}

function onRefresh(){
    onRequest({
        pagination: table.pagination,
        filter: null,
    });
}
</script>
