<template>
    <div>
        <b-card class="text-center">
            <!-- Header -->
            <b-row class="d-flex align-items-center">
                <b-col class="col-lg-6 col-md-12">
                    <b-row>
                        <b-col class="py-1">
                            <!-- Keyword -->
                            <b-form-input
                                v-model="keyword"
                                type="text"
                                placeholder="Cari"
                                class="w-100"
                            ></b-form-input>
                        </b-col>
                        <b-col class="py-1">
                            <!-- Month -->
                            <el-date-picker
                            v-model="filterMonth"
                            @change="reloadTable(tableParams)"
                            type="month"
                            class="w-100"
                            placeholder="Pick a month"
                            format="MMM yyyy"
                            value-format="DD-MM-yyyy"
                            />
                        </b-col>
                    </b-row>
                </b-col>
                <b-col class="col-lg-6 col-md-12 text-right">
                    <!-- Add -->
                    <b-button v-show="user.job_position.role_id == 3" @click="create" variant="success" size="sm"
                        ><b-icon icon="plus"></b-icon> Tambah</b-button
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

                <template v-slot:cell(code)="row">
                    <span>{{ row.value }}</span><br>
                    <b-badge v-if="!row.value" pill variant="warning">Menunggu</b-badge>
                    <b-badge v-if="row.value == 1" pill variant="primary">Dalam Proses</b-badge>
                    <b-badge v-if="row.value == 2" pill variant="success">Selesai</b-badge>
                    <b-badge v-if="row.value == 3" pill variant="danger">Gagal</b-badge>
                    <b-badge v-if="row.value == 4" pill variant="info">Jadwal Ulang</b-badge>
                </template>

                <template v-slot:cell(requester)="row">
                    {{ row.value.first_name }} {{ row.value.last_name }}
                    <div><em><small><span title="NIP">{{ row.value.nip }}</span> - <span title="Jabatan">{{ row.value.job_position.name }}</span></small></em></div>
                </template>

                <template v-slot:cell(job_category)="row">
                    {{ row.value.name }}
                </template>

                <template v-slot:cell(customer)="row">
                    <a :href="row.value.gmaps_url" target="_blank" rel="noopener noreferrer">{{ row.value.name }}</a>
                </template>

                <template v-slot:cell(date)="row">
                    {{ formatDate(row.item.start_date) }} - {{ formatDate(row.item.end_date) }}
                </template>

                <template v-slot:cell(status)="row">
                    <b-badge v-if="row.item.result == 2" pill variant="success">Selesai</b-badge>
                    <b-badge v-else-if="row.item.status == 1" pill variant="warning">Menunggu</b-badge>
                    <b-badge v-else-if="row.item.status == 2" pill variant="success">Disetujui</b-badge>
                    <b-badge v-else-if="row.item.status == 3" pill variant="secondary">Dibatalkan</b-badge>
                    <b-badge v-else-if="row.item.status == 4" pill variant="danger">Ditolak</b-badge>
                </template>

                <template v-slot:cell(action)="row">
                    <a v-if="row.item.status == 1 && user.job_position.role_id == 2" href="#" @click="viewApproval(row.item.id)"><b-badge
                        title="Persetujuan"
                        pill
                        variant="primary"
                        ><b-icon icon="person-check"></b-icon
                    ></b-badge></a>
                    <a v-if="row.item.status == 2 && user.job_position.role_id == 4" href="#" @click="print(row.item.id)"><b-badge
                        title="Cetak"
                        pill
                        variant="primary"
                        ><b-icon icon="printer-fill"></b-icon
                    ></b-badge></a>
                    <a v-if="row.item.status == 2 && user.job_position.role_id == 4" href="#" @click="viewReport(row.item.id)">
                        <b-badge
                        title="Laporan"
                        pill
                        variant="warning"
                        ><b-icon icon="file-earmark-check"></b-icon
                    ></b-badge></a>
                    <a v-if="row.item.status == 1 && user.job_position.role_id == 3" href="#" @click="remove(row.item.id)"><b-badge
                        title="Hapus"
                        pill
                        variant="danger"
                        ><b-icon icon="trash"></b-icon
                    ></b-badge></a>
                    <a v-if="row.item.status == 4 && user.job_position.role_id == 3" href="#" @click="view(row.item.id)">
                        <b-badge
                            title="Ajukan Ulang"
                            pill
                            variant="primary"
                            ><b-icon icon="arrow-clockwise"></b-icon
                        ></b-badge>
                    </a>
                    <a href="#" @click="detail(row.item.id)">
                        <b-badge
                            title="Tampilkan"
                            pill
                            variant="success"
                            ><b-icon icon="eye"></b-icon
                        ></b-badge>
                    </a>
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

        <!-- Modal Add Project -->
        <b-modal
            id="modal-form-business-trip"
            size="xl"
            :title="fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <formComponent :modelId="modelId" />
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Batal</b-button
                >
                <button class="btn btn-primary" @click.prevent="submit">
                    <b-spinner
                        v-if="loadingProcess"
                        small
                        class="mr-2"
                    ></b-spinner>
                    <i v-else class="far fa-save mr-2"></i>
                    <span v-if="modelId > 0">Simpan</span>
                    <span v-else>Ajukan</span>
                </button>
            </template>
        </b-modal>
        <!-- End -->

        <!-- Modal Cancel -->
        <b-modal
            id="modal-form-business-trip-cancel"
            size="md"
            :title="'Batalkan '+fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <cancelComponent/>
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Batal</b-button
                >
                <button class="btn btn-primary" @click.prevent="canceling">
                    <b-spinner
                        v-if="loadingProcess"
                        small
                        class="mr-2"
                    ></b-spinner>
                    <i v-else class="far fa-save mr-2"></i> Simpan
                </button>
            </template>
        </b-modal>
        <!-- End -->

        <!-- Modal Detail -->
        <b-modal
            id="modal-form-business-trip-detail"
            size="lg"
            :title="'Detail '+fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <detailComponent :modelId="modelId"/>
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i> Kembali</b-button
                >
            </template>
        </b-modal>
        <!-- End -->

        <!-- Modal Approval -->
        <b-modal
            id="modal-form-business-trip-approval"
            size="lg"
            :title="'Persetujuan '+fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <approvalComponet :modelId="modelId"/>
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i> Kembali</b-button
                >
                <button class="btn btn-primary" @click.prevent="approval">
                    <b-spinner
                        v-if="loadingProcess"
                        small
                        class="mr-2"
                    ></b-spinner>
                    <i v-else class="far fa-save mr-2"></i> Simpan
                </button>
            </template>
        </b-modal>
        <!-- End -->

        <!-- Modal Reporting -->
        <b-modal
            id="modal-form-business-trip-reporting"
            size="xl"
            :title="'Laporan '+fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <reportingComponent :modelId="modelId"/>
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i> Kembali</b-button
                >
                <button v-if="form.result == 1" class="btn btn-primary" @click.prevent="reporting">
                    <b-spinner
                        v-if="loadingProcess"
                        small
                        class="mr-2"
                    ></b-spinner>
                    <i v-else class="far fa-save mr-2"></i> Simpan
                </button>
            </template>
        </b-modal>
        <!-- End -->

        <!-- Modal Print Letter -->
        <b-modal
            id="modal-print-preview-letter"
            size="xl"
            :title="'Surat Perintah '+fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <iframe srcdoc="Loading..." onload="this.removeAttribute('srcdoc')" :src="`/api/business-trip-letter/${modelId}`" style="height:500px;width:100%;" frameborder="0"></iframe>
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
import { mapMutations, mapActions, mapState, mapGetters } from "vuex";
import * as notify from "@app/utils/notify";
import moment from "moment";
import formComponent from "./form.vue";
import cancelComponent from "./cancel.vue"
import detailComponent from "./detail.vue"
import approvalComponet from "./approval.vue"
import reportingComponent from "./reporting.vue"

export default {
    components: {
        formComponent,
        cancelComponent,
        detailComponent,
        approvalComponet,
        reportingComponent
    },
    created() {
        this.keyword = this.$route.query.code ? this.$route.query.code : '';
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
                { key: "customer", label: "Lokasi Tujuan", sortable: false },
                { key: "date", label: "Tanggal", sortable: false },
                { key: "job_category", label: "Kategori Pekerjaan", sortable: false },
                { key: "requester", label: "Pemohon", sortable: false, tdClass:"text-left" },
                { key: "status", label: "Status", sortable: false },
                {
                    key: "action",
                    label: "Aksi",
                    thStyle: "text-align: center; width: 80px;",
                    sortable: false
                }
            ],
            loadingProcess: false,
            keyword: "",
            modelId: "",
            fileTitle: "Perjalan Dinas",
            filterMonth: moment().format('DD-MM-yyyy')
        };
    },
    computed: {
        ...mapState("businessTripApplication", {
            collection: state => state.collection,
            isBusy: state => state.isBusy,
            form: state => state.form,
        }),
        ...mapGetters(['user']),
        tableParams: {
            get() {
                //MENGAMBIL VALUE PAGE DARI VUEX MODULE
                return this.$store.state.admin.businessTripApplication.tableParams;
            },
            set(params) {
                this.$store.commit("businessTripApplication/SET_TABLE_PARAMS", params);
            }
        }
    },
    mounted(){
        this.CLEAR_ERRORS()
        this.CLEAR_FORM()
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
        ...mapMutations('businessTripApplication', ['CLEAR_FORM']),
        ...mapActions("businessTripApplication", [
            "load",
            "show",
            "store",
            "update",
            "destroy",
            "cancel",
            "approve",
            "report"
        ]),
        create() {
            this.$bvModal.show("modal-form-business-trip");
        },
        viewCancel(id){
            this.modelId = id;
            this.$bvModal.show("modal-form-business-trip-cancel");
        },
        detail(id){
            this.modelId = id;
            this.$bvModal.show("modal-form-business-trip-detail");
        },
        viewApproval(id){
            this.modelId = id;
            this.$bvModal.show("modal-form-business-trip-approval");
        },
        viewReport(id){
            this.modelId = id;
            this.$bvModal.show("modal-form-business-trip-reporting");
        },
        view(id) {
            this.modelId = id;
            this.create();
        },
        print(id){
            this.modelId = id;
            this.$bvModal.show("modal-print-preview-letter");
        },
        hideModal(loading) {
            this.modelId = "";
            if (loading) {
                this.reloadTable(this.tableParams);
            }
            this.$bvModal.hide("modal-form-business-trip");
            this.$bvModal.hide("modal-form-business-trip-cancel");
            this.$bvModal.hide("modal-form-business-trip-detail");
            this.$bvModal.hide("modal-form-business-trip-approval");
            this.$bvModal.hide("modal-form-business-trip-reporting");
            this.$bvModal.hide("modal-print-preview-letter");
        },
        reloadTable(params, keyword = false, filter = false) {
            this.load({
                keyword: this.keyword,
                month: this.filterMonth,
                company_id: this.company_id,
                payroll_schedule_id: this.payroll_schedule_id,
                payroll_transaction_id: this.payroll_transaction_id,
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
        reporting: _.debounce(function() {
            this.loadingProcess = true;
            this.report(this.modelId)
            .then(r => {
                this.hideModal(true);
                notify.success(this.fileTitle, "simpan");
                this.loadingProcess = false;
            })
            .catch(e => {
                notify.error(this.fileTitle, "simpan");
                this.loadingProcess = false;
            });
        }, 500),
        canceling: _.debounce(function() {
            this.loadingProcess = true;
            this.cancel(this.modelId)
            .then(r => {
                this.hideModal(true);
                notify.success(this.fileTitle, "simpan");
                this.loadingProcess = false;
            })
            .catch(e => {
                notify.error(this.fileTitle, "simpan");
                this.loadingProcess = false;
            });
        }, 500),
        approval: _.debounce(function() {
            this.loadingProcess = true;
            this.approve(this.modelId)
            .then(r => {
                this.hideModal(true);
                notify.success(this.fileTitle, "simpan");
                this.loadingProcess = false;
            })
            .catch(e => {
                notify.error(this.fileTitle, "simpan");
                this.loadingProcess = false;
            });
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
        },
        formatDate: (date) => {
            moment.lang("id")
            return moment(date).format('DD MMM YYYY')
        }
    }
};
</script>
