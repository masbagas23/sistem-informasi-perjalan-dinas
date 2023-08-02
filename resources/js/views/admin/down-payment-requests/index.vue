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
                    <b-button v-show="user.job_position.role_id == 4" @click="create" variant="success" size="sm"
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
                    {{ row.value }}
                </template>
                
                <template v-slot:cell(nominal)="row">
                    Rp {{ formatCurrency(row.value) }}
                </template>

                <template v-slot:cell(created_at)="row">
                    {{ formatDate(row.value) }}
                </template>

                <template v-slot:cell(requester)="row">
                    {{ row.value.first_name }} {{ row.value.middle_name }} {{ row.value.last_name }}
                </template>    

                <template v-slot:cell(status)="row">
                    <b-badge v-if="row.item.status == 1" pill variant="warning">Menunggu</b-badge>
                    <b-badge v-if="row.item.status == 2" pill variant="success">Disetujui</b-badge>
                    <b-badge v-if="row.item.status == 3" pill variant="secondary">Dibatalkan</b-badge>
                    <b-badge v-if="row.item.status == 4" pill variant="danger">Ditolak</b-badge>
                </template>         

                <template v-slot:cell(action)="row">
                    <a v-if="row.item.status == 1 && user.job_position.role_id == 3" href="#" @click="viewApproval(row.item.id)">
                        <b-badge
                            title="Persetujuan"
                            pill
                            variant="primary"
                            ><b-icon icon="person-check"></b-icon
                        ></b-badge>
                    </a>
                    <a v-if="row.item.status == 1 && user.job_position.role_id == 4" href="#" @click="remove(row.item.id)">
                        <b-badge
                            title="Hapus"
                            pill
                            variant="danger"
                            ><b-icon icon="trash"></b-icon
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
                    <!-- <b-dropdown variant="secondary" size="sm" right>
                        <b-dropdown-item @click="detail(row.item.id)">
                            <b-badge
                                title="Tampilkan"
                                pill
                                variant="success"
                                ><b-icon icon="eye"></b-icon
                            ></b-badge>
                            <span> Tampilkan</span>
                        </b-dropdown-item>
                        <b-dropdown-item @click="viewApproval(row.item.id)">
                            <b-badge
                                title="Persetujuan"
                                pill
                                variant="info"
                                ><b-icon icon="person-check"></b-icon
                            ></b-badge>
                            <span> Persetujuan</span>
                        </b-dropdown-item>
                        <b-dropdown-item @click="view(row.item.id)">
                            <b-badge
                                title="Edit"
                                pill
                                variant="primary"
                                ><b-icon icon="pencil-square"></b-icon
                            ></b-badge>
                            <span> Edit</span>
                        </b-dropdown-item>
                        <b-dropdown-item @click="viewCancel(row.item.id)">
                            <b-badge
                                title="Batalkan"
                                pill
                                variant="warning"
                                ><b-icon icon="x-circle"></b-icon
                            ></b-badge>
                            <span> Batalkan</span>
                        </b-dropdown-item>
                        <b-dropdown-item @click="remove(row.item.id)">
                            <b-badge
                                title="Hapus"
                                pill
                                variant="danger"
                                ><b-icon icon="trash"></b-icon
                            ></b-badge>
                            <span> Hapus</span>
                        </b-dropdown-item>
                    </b-dropdown> -->
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

        <!-- Modal Form -->
        <b-modal
            id="modal-form-vehicle-loan"
            size="md"
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
                    <i v-else class="far fa-save mr-2"></i> Simpan
                </button>
            </template>
        </b-modal>

        <!-- Modal Detail -->
        <b-modal
            id="modal-detail-vehicle-loan"
            size="lg"
            :title="fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <detailComponent :modelId="modelId" />
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Kembali</b-button
                >
            </template>
        </b-modal>

        <!-- Modal Approval -->
        <b-modal
            id="modal-approval-vehicle-loan"
            size="lg"
            :title="'Persetujuan ' + fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <approvalComponent :modelId="modelId" />
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Kembali</b-button
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

        <!-- Modal Cancel -->
        <b-modal
            id="modal-cancel-vehicle-loan"
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
    </div>
</template>
<script>
import { mapMutations, mapActions, mapState, mapGetters } from "vuex";
import formComponent from "./form.vue";
import * as notify from "@app/utils/notify";
import moment from "moment";
import {formatDate, formatCurrency} from "@app/utils/formatter"
import detailComponent from "./detail.vue"
import approvalComponent from "./approval.vue"
import cancelComponent from "./cancel.vue"

export default {
    components: {
        formComponent,
        detailComponent,
        approvalComponent,
        cancelComponent
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
                { key: "nominal", label: "Nominal", sortable: false },
                { key: "created_at", label: "Tanggal Permintaan", sortable: false },
                { key: "requester", label: "Pemohon", sortable: false },
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
            fileTitle: "Uang Muka",
            filterMonth: moment().format('DD-MM-yyyy')
        };
    },
    computed: {
        ...mapState("downPaymentRequest", {
            collection: state => state.collection,
            isBusy: state => state.isBusy,
        }),
        ...mapGetters(['user']),
        tableParams: {
            get() {
                //MENGAMBIL VALUE PAGE DARI VUEX MODULE
                return this.$store.state.admin.downPaymentRequest.tableParams;
            },
            set(params) {
                this.$store.commit("downPaymentRequest/SET_TABLE_PARAMS", params);
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
        ...mapActions("downPaymentRequest", [
            "load",
            "show",
            "store",
            "update",
            "destroy",
            "cancel",
            "approve"
        ]),
        formatDate,formatCurrency,
        create() {
            this.$bvModal.show("modal-form-vehicle-loan");
        },
        view(id) {
            this.modelId = id;
            this.create();
        },
        detail(id){
            this.modelId = id;
            this.$bvModal.show("modal-detail-vehicle-loan");
        },
        viewCancel(id){
            this.modelId = id;
            this.$bvModal.show("modal-cancel-vehicle-loan");
        },
        viewApproval(id){
            this.modelId = id;
            this.$bvModal.show("modal-approval-vehicle-loan");
        },
        hideModal(loading) {
            this.modelId = "";
            if (loading) {
                this.reloadTable(this.tableParams);
            }
            this.$bvModal.hide("modal-form-vehicle-loan");
            this.$bvModal.hide("modal-approval-vehicle-loan");
            this.$bvModal.hide("modal-detail-vehicle-loan");
            this.$bvModal.hide("modal-cancel-vehicle-loan");
        },
        reloadTable(params, keyword = false, filter = false) {
            this.load({
                keyword: this.keyword,
                company_id: this.company_id,
                month: this.filterMonth,
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
        }
    }
};
</script>
