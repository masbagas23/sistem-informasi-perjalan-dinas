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
                
                <template v-slot:cell(total_nominal)="row">
                    Rp {{ formatCurrency(row.value) }}
                </template>
                
                <template v-slot:cell(remaining_cost)="row">
                    Rp {{ formatCurrency(row.value) }}
                </template>

                <template v-slot:cell(reimburse_cost)="row">
                    Rp {{ formatCurrency(row.value) }}
                    <a href="#" @click="viewReimburse(row.item.id)"><div>
                        <b-badge v-if="row.item.status_reimburse == 1" pill variant="warning">Menunggu Bukti Transfer</b-badge>
                        <b-badge v-if="row.item.status_reimburse == 2" pill variant="success">Lihat Bukti Transfer</b-badge>
                    </div></a>
                </template>
                
                <template v-slot:cell(status)="row">
                    <b-badge v-if="row.item.status == 1" pill variant="warning">Menunggu Validasi</b-badge>
                    <b-badge v-if="row.item.status == 2" pill variant="success">Selesai</b-badge>
                </template>

                <template v-slot:cell(application)="row">
                    {{ row.value.code }} - {{ row.value.customer ? row.value.customer.name : ''}}
                    <div><em><small>{{ formatDate(row.value.start_date) }} - {{ formatDate(row.value.end_date) }} ({{row.value.total_day}} Hari)</small></em></div>
                </template>

                <template v-slot:cell(action)="row">
                    <a v-if="row.item.status == 1 && user.job_position.role_id == 5" href="#" @click="viewValidation(row.item.id)">
                        <b-badge
                            title="Validasi"
                            pill
                            variant="primary"
                            ><b-icon icon="person-check"></b-icon
                        ></b-badge>
                    </a>
                    <a href="#" v-if="row.item.status == 1 && user.job_position.role_id == 4" @click="view(row.item.id)">
                        <b-badge
                            title="Edit"
                            pill
                            variant="primary"
                            ><b-icon icon="pencil-square"></b-icon
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
                    <a href="#" @click="viewValidation(row.item.id, 'detail')">
                        <b-badge
                            title="Detail"
                            pill
                            variant="success"
                            ><b-icon icon="eye"></b-icon
                        ></b-badge>
                    </a>
                    <!-- <b-dropdown variant="secondary" size="sm" right>
                        <b-dropdown-item @click="viewValidation(row.item.id)">
                            <b-badge
                                title="Persetujuan"
                                pill
                                variant="success"
                                ><b-icon icon="check-circle"></b-icon
                            ></b-badge>
                            <span> Validasi</span>
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
            id="modal-form-expense"
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
                    <i v-else class="far fa-save mr-2"></i> Simpan
                </button>
            </template>
        </b-modal>

        <!-- Modal Validation -->
        <b-modal
            id="modal-validation-expense"
            size="xl"
            :title="fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <validationComponent :mode="mode" :modelId="modelId" />
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Kembali</b-button
                >
                <button v-if="mode != 'detail'" class="btn btn-primary" @click.prevent="validating">
                    <b-spinner
                        v-if="loadingProcess"
                        small
                        class="mr-2"
                    ></b-spinner>
                    <i v-else class="far fa-save mr-2"></i> Simpan
                </button>
            </template>
        </b-modal>

        <!-- Modal Reimburse -->
        <b-modal
            id="modal-reimburse-expense"
            size="md"
            :title="'Reimburse ' + fileTitle"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
        >
            <reimburseComponent :modelId="modelId"/>
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Kembali</b-button
                >
                <button v-if="form.status_reimburse == 1" class="btn btn-primary" @click.prevent="reimbursing">
                    <b-spinner
                        v-if="loadingProcess"
                        small
                        class="mr-2"
                    ></b-spinner>
                    <i v-else class="far fa-save mr-2"></i> Simpan
                </button>
            </template>
        </b-modal>
    </div>
</template>
<script>
import { mapMutations, mapActions, mapState, mapGetters } from "vuex";
import formComponent from "./form.vue";
import validationComponent from "./validation.vue"
import reimburseComponent from "./reimburse.vue"
import * as notify from "@app/utils/notify";
import moment from "moment";
import {formatDate, formatCurrency} from "@app/utils/formatter"

export default {
    components: {
        formComponent,
        validationComponent,
        reimburseComponent
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
                { key: "application", label: "Perjalan Dinas", sortable: false },
                { key: "total_nominal", label: "Total Pengeluaran", sortable: false },
                { key: "remaining_cost", label: "Sisa", sortable: false },
                { key: "reimburse_cost", label: "Kurang", sortable: false },
                { key: "status", label: "Status", sortable: false },
                {
                    key: "action",
                    label: "Aksi",
                    thStyle: "text-align: center; width: 80px;",
                    sortable: false,
                    isNotAdmin: true
                }
            ],
            loadingProcess: false,
            keyword: "",
            modelId: "",
            mode:"",
            fileTitle: "Biaya Pengeluaran",
            filterMonth: moment().format('DD-MM-yyyy')
        };
    },
    computed: {
        ...mapState("expense", {
            collection: state => state.collection,
            isBusy: state => state.isBusy,
            form: state => state.form,
        }),
        ...mapGetters(["user"]),
        tableParams: {
            get() {
                //MENGAMBIL VALUE PAGE DARI VUEX MODULE
                return this.$store.state.admin.expense.tableParams;
            },
            set(params) {
                this.$store.commit("expense/SET_TABLE_PARAMS", params);
            }
        },
        filterFields(){
            if(this.user.job_position.role_id == 5){
                return this.fields.filter(item => !item.isNotAdmin)
            }else{
                return this.fields
            }
        },
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
        ...mapMutations('expense', ['CLEAR_FORM']),
        ...mapActions("expense", [
            "load",
            "show",
            "store",
            "update",
            "destroy",
            "validate",
            "reimburse"
        ]),
        formatDate,formatCurrency,
        create() {
            this.$bvModal.show("modal-form-expense");
        },
        view(id) {
            this.modelId = id;
            this.create();
        },
        viewValidation(id,detail) {
            this.modelId = id;
            this.mode = detail
            this.$bvModal.show("modal-validation-expense");
        },
        viewReimburse(id) {
            this.modelId = id;
            this.$bvModal.show("modal-reimburse-expense");
        },
        hideModal(loading) {
            this.modelId = "";
            this.mode = "";
            if (loading) {
                this.reloadTable(this.tableParams);
            }
            this.$bvModal.hide("modal-form-expense");
            this.$bvModal.hide("modal-validation-expense");
            this.$bvModal.hide("modal-reimburse-expense");

        },
        reloadTable(params, keyword = false, filter = false) {
            this.load({
                keyword: this.keyword,
                company_id: this.company_id,
                payroll_schedule_id: this.payroll_schedule_id,
                payroll_transaction_id: this.payroll_transaction_id,
                page: keyword ? (this.tableParams.page = 1) : params.page,
                per_page: params.per_page,
                order_column: params.order_column,
                month: this.filterMonth,
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
        validating: _.debounce(function() {
            this.loadingProcess = true;
            this.validate(this.modelId)
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
        reimbursing: _.debounce(function() {
            this.loadingProcess = true;
            this.reimburse(this.modelId)
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
