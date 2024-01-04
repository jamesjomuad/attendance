<template>
    <q-page padding>
        <!-- Table -->
        <div class="col-auto">
            <q-table
                :dark="$q.dark.isActive"
                flat
                bordered
                binary-state-sort
                title="Announcements"
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
                    <!-- <q-input
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
                    </q-input> -->
                    <q-btn
                        round
                        size="md"
                        color="primary"
                        class="q-ml-sm"
                        icon="add"
                        to="/system/announcement/create">
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
                <template #body-cell-type="props">
                    <q-td :props="props">
                        <q-badge class="q-pa-sm" :color="ui.typeBg(props.row.type)">{{ props.row.type }}</q-badge>
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
import { ref, reactive, onMounted, computed } from "vue";
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'


const $router = useRouter();
const $q = useQuasar()
const ui = reactive({
    loading: false,
    typeBg(type){
        let colors = {
            'Emergency': 'red',
            'Alert': 'warning',
            'Update': 'green-4',
            'Reminder': 'info'
        }
        return colors[type]
    }
})
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
            label: "Title",
            name: "title",
            field: "title",
            align: 'left',
            sortable: true,
        },
        {
            label: 'Type',
            name: 'type',
            field: 'type',
            sortable: false,
            align: 'left',
        },
        {
            label: 'Start Date Time',
            field: 'date_start',
            sortable: false,
            align: 'left',
        },
        {
            label: 'End Date Time',
            field: 'date_end',
            sortable: false,
            align: 'left',
        },
        {
            label: 'Created',
            field: 'created_at',
            sortable: true,
            align: 'left',
            format: (val, row) => {
                return moment(val).format("YYYY-MM-DD");
            },
        },
        {
            label: 'Updated',
            field: 'updated_at',
            sortable: true,
            align: 'left',
            format: (val, row) => {
                return moment(val).format("YYYY-MM-DD");
            }
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
        const { data } = await axios.get(`/api/announcement`, {params})

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
    $router.push(`/system/announcement/${row.id}`)
}

function onRefresh(){
    onRequest({
        pagination: table.pagination,
        filter: null,
    });
}
</script>
