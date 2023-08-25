<template>
    <div>
        <b-card class="text-center">
            <!-- Header -->
            <b-row class="d-flex align-items-center">
                <b-col cols="8" class="text-left">
                    <!-- Keyword -->
                    <el-date-picker
                    v-model="filterMonth"
                    @change="reloadTable(tableParams)"
                    type="month"
                    placeholder="Pick a month"
                    format="MMM yyyy"
                    value-format="DD-MM-yyyy"
                    />
                </b-col>
                <b-col cols="4" class="text-right">
                    <!-- Print -->
                    <b-button @click="print" variant="success" size="sm"
                        ><b-icon icon="printer-fill"></b-icon> Cetak</b-button
                    >
                    <!-- Reload -->
                    <b-button
                        @click="handleSynchronize"
                        variant="primary"
                        size="sm"
                    >
                        <b-icon icon="arrow-clockwise"></b-icon>
                    </b-button>
                </b-col>
            </b-row>
            <!-- Table -->
            <b-table
                :items="collection.data"
                :busy.sync="isBusy"
                :fields="fields"
                :sort-by.sync="tableParams.order_column"
                :sort-desc.sync="tableParams.order_direction"
                :tbody-tr-class="rowClass"
                responsive="sm"
                class="py-2"
                show-empty
                hover
                bordered
                breceptioned
                small
            >
                <template #table-busy>
                    <div class="text-center text-info my-2">
                        <b-spinner class="align-middle"></b-spinner>
                        <strong>Loading...</strong>
                    </div>
                </template>

                <template v-slot:cell(index)="row">
                    {{ row.index + 1 }}
                </template>
                
                <template v-slot:cell(customer)="row">
                    {{ row.value.name }}
                </template>

                <template v-slot:cell(users)="row">
                    {{ row.value[0].user.first_name }} {{ row.value[0].user.last_name }}
                </template>

                <template v-slot:cell(job_category)="row">
                    {{ row.value.name }}
                </template>

                <template v-slot:cell(total_expense)="row">
                    Rp {{ formatCurrency(row.item.expenses_sum_total_nominal) }}
                </template>

                <template v-slot:cell(result)="row">
                    <b-badge v-if="row.value == 1" pill variant="warning">Menunggu</b-badge>
                    <b-badge v-if="row.value == 2" pill variant="success">Selesai</b-badge>
                    <b-badge v-if="row.value == 3" pill variant="secondary">Tertunda</b-badge>
                    <b-badge v-if="row.value == 4" pill variant="warning">Jadwal Ulang</b-badge>
                </template>

                <template v-slot:cell(date)="row">
                    {{ formatDate(row.item.start_date) }} - {{ formatDate(row.item.end_date) }}
                </template>

                <template v-slot:cell(action)="row">
                    <b-badge
                        title="Tampilkan"
                        @click="view(row.item.id)"
                        class="btn"
                        pill
                        variant="success"
                        ><b-icon icon="eye"></b-icon
                    ></b-badge>
                </template>
            </b-table>
            <div
                class="card-footer px-0 bg-white d-flex justify-content-between align-items-center"
            >
                <div>
                    <b-form-select
                        v-model="tableParams.per_page"
                        :options="tableParams.pageOptions"
                    ></b-form-select>
                </div>
                <div class="d-none d-lg-block">
                    <p v-if="collection.data.length > 0">
                        Menampilkan data ke {{collection.meta.from}} sampai {{collection.meta.to}} dari total {{collection.meta.total}} data
                    </p>
                </div>
                <div>
                    <b-pagination
                        v-model="tableParams.page"
                        pills
                        :total-rows="collection.meta.total"
                        :per-page="collection.meta.per_page"
                        v-if="collection.data && collection.data.length > 0"
                    ></b-pagination>
                </div>
            </div>
        </b-card>

        <!-- Modal Detail -->
        <b-modal
            id="modal-detail-report-trip"
            size="xl"
            :title="fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <detailComponent :modelId="modelId" />
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Batal</b-button
                >
            </template>
        </b-modal>
        <!-- End -->

        <!-- Modal Print -->
        <b-modal
            id="modal-print-report-trip"
            size="xl"
            :title="'Laporan '+fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <iframe srcdoc="Loading..." onload="this.removeAttribute('srcdoc')" :src="`/api/business-trip-report?month=${filterMonth}`" style="height:500px;width:100%;" frameborder="0"></iframe>
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i> Kembali</b-button
                >
            </template>
        </b-modal>
        <!-- End -->
    </div>
</template>
<script>
import { mapMutations, mapActions, mapState } from "vuex";
import detailComponent from "./detail.vue";
import * as notify from "@app/utils/notify";
import moment from "moment";
import {formatDate, formatCurrency} from "@app/utils/formatter"

export default {
    components: {
        detailComponent
    },
    created() {
        this.reloadTable(this.tableParams);
    },
    data() {
        return {
            fields: [
                {
                    key: "index",
                    label: "#",
                    thStyle: "text-align: center; width: 80px;",
                    sortable: false
                },
                { key: "code", label: "Kode", sortable: false },
                { key: "customer", label: "Lokasi", sortable: false },
                { key: "job_category", label: "Alasan Dinas", sortable: false },
                { key: "date", label: "Tanggal", sortable: false },
                { key: "users", label: "Koordinator", sortable: false },
                { key: "total_expense", label: "Total Pengeluaran", sortable: false },
                { key: "result", label: "Status", sortable: false },
                // {
                //     key: "action",
                //     label: "Aksi",
                //     thStyle: "text-align: center; width: 80px;",
                //     sortable: false
                // }
            ],
            loadingProcess: false,
            keyword: "",
            modelId: "",
            fileTitle: "Rekap Perjalan Dinas",
            filterMonth: moment().format('DD-MM-yyyy')
        };
    },
    computed: {
        ...mapState("reportTrip", {
            collection: state => state.collection,
            isBusy: state => state.isBusy,
        }),
        tableParams: {
            get() {
                //MENGAMBIL VALUE PAGE DARI VUEX MODULE
                return this.$store.state.admin.reportTrip.tableParams;
            },
            set(params) {
                this.$store.commit("reportTrip/SET_TABLE_PARAMS", params);
            }
        }
    },
    mounted(){
        this.CLEAR_ERRORS()
    },
    watch: {
        tableParams: {
            handler: function(params) {
                this.reloadTable(params);
            },
            deep: true
        },
        keyword() {
            this.reloadTable(this.tableParams, true);
        }
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapActions("reportTrip", [
            "load",
            "show",
            "store",
            "update",
            "destroy",
        ]),
        formatDate,formatCurrency,
        create() {
            this.$bvModal.show("modal-detail-report-trip");
        },
        print() {
            this.$bvModal.show("modal-print-report-trip");
        },
        view(id) {
            this.modelId = id;
            this.create();
        },
        hideModal(loading) {
            this.modelId = "";
            if (loading) {
                this.reloadTable(this.tableParams);
            }
            this.$bvModal.hide("modal-detail-report-trip");
            this.$bvModal.hide("modal-print-report-trip");
        },
        reloadTable(params, keyword = false, filter = false) {
            this.load({
                keyword: this.keyword,
                company_id: this.company_id,
                month: this.filterMonth,
                page: keyword ? (this.tableParams.page = 1) : params.page,
                per_page: params.per_page,
                order_column: params.order_column,
                order_direction: params.order_direction
            });
        },
        rowClass(item, type) {
            if (!item || type !== "row") return;

            let current_time = moment(),
                item_time = moment(item.updated_at),
                duration = moment.duration(current_time.diff(item_time));

            if (duration.as("seconds") < 10) return "table-success";
        },
        handleSynchronize() {
            this.reloadTable(this.tableParams);
        },
        submit: _.debounce(function() {
            this.loadingProcess = true;
            if (!this.modelId) {
                this.store()
                    .then(r => {
                        this.hideModal(true);
                        notify.success(this.fileTitle, "tambah");
                        this.loadingProcess = false;
                    })
                    .catch(e => {
                        notify.error(this.fileTitle, "tambah");
                        this.loadingProcess = false;
                    });
            } else {
                this.update(this.modelId)
                    .then(r => {
                        this.hideModal(true);
                        notify.success(this.fileTitle, "simpan");
                        this.loadingProcess = false;
                    })
                    .catch(e => {
                        notify.error(this.fileTitle, "simpan");
                        this.loadingProcess = false;
                    });
            }
        }, 500),
        remove(id) {
            this.$confirm(
                this.fileTitle + " akan dihapus?",
                "Peringatan",
                "warning",
                1
            )
                .then(() => {
                    this.destroy(id).then(() => {
                        this.hideModal(true);
                        notify.success(this.fileTitle, "hapus");
                    });
                })
                .catch(() => {
                        notify.error(this.fileTitle, "hapus");
                });
        }
    }
};
</script>
