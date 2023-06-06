<template>
    <div>
        <b-card class="text-center">
            <!-- Header -->
            <b-row class="d-flex align-items-center">
                <b-col cols="8">
                    <!-- Keyword -->
                    <b-form-input
                        v-model="keyword"
                        type="text"
                        placeholder="Cari"
                        class="h-75 w-25"
                    ></b-form-input>
                </b-col>
                <b-col cols="4" class="text-right">
                    <!-- Add -->
                    <b-button @click="create" variant="success" size="sm"
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
                    <b-badge v-if="row.item.status == 1" pill variant="warning">Menunggu</b-badge>
                    <b-badge v-if="row.item.status == 2" pill variant="success">Disetujui</b-badge>
                    <b-badge v-if="row.item.status == 3" pill variant="secondary">Dibatalkan</b-badge>
                    <b-badge v-if="row.item.status == 4" pill variant="danger">Ditolak</b-badge>
                </template>

                <template v-slot:cell(requester)="row">
                    {{ row.value.first_name }}
                </template>

                <template v-slot:cell(job_category)="row">
                    {{ row.value.name }}
                </template>

                <template v-slot:cell(date)="row">
                    {{ formatDate(row.item.start_date) }} - {{ formatDate(row.item.end_date) }}
                </template>

                <template v-slot:cell(result)="row">
                    <b-badge v-if="!row.value" pill variant="warning">Menunggu</b-badge>
                    <b-badge v-if="row.value == 1" pill variant="primary">Dalam Proses</b-badge>
                    <b-badge v-if="row.value == 2" pill variant="success">Selesai</b-badge>
                    <b-badge v-if="row.value == 3" pill variant="danger">Gagal</b-badge>
                    <b-badge v-if="row.value == 4" pill variant="info">Jadwal Ulang</b-badge>
                </template>

                <template v-slot:cell(action)="row">
                    <b-dropdown variant="warning" size="sm" right>
                        <b-dropdown-item @click="view(row.item.id)">
                            <b-badge
                                title="Tampilkan"
                                pill
                                variant="success"
                                ><b-icon icon="eye"></b-icon
                            ></b-badge>
                            <span> Tampilkan</span>
                        </b-dropdown-item>
                        <b-dropdown-item @click="view(row.item.id)">
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
                        <b-dropdown-item @click="view(row.item.id)">
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
                    </b-dropdown>
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
                <div>
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
    </div>
</template>
<script>
import { mapMutations, mapActions, mapState } from "vuex";
import formComponent from "./form.vue";
import * as notify from "@app/utils/notify";
import moment from "moment";

export default {
    components: {
        formComponent
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
                { key: "date", label: "Tanggal", sortable: false },
                { key: "job_category", label: "Kategori Pekerjaan", sortable: false },
                { key: "requester", label: "Pemohon", sortable: false },
                { key: "result", label: "Status", sortable: false },
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
            fileTitle: "Perjalan Dinas"
        };
    },
    computed: {
        ...mapState("businessTripApplication", {
            collection: state => state.collection,
            isBusy: state => state.isBusy,
        }),
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
        ...mapActions("businessTripApplication", [
            "load",
            "show",
            "store",
            "update",
            "destroy",
        ]),
        create() {
            this.$bvModal.show("modal-form-business-trip");
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
            this.$bvModal.hide("modal-form-business-trip");
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
        },
        formatDate: (date) => {
            moment.lang("id")
            return moment(date).format('DD MMM YYYY')
        }
    }
};
</script>
