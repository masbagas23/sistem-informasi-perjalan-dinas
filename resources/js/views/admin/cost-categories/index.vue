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

                <template v-slot:cell(role)="row">
                    {{ row.value.name }}
                </template>

                <template v-slot:cell(action)="row">
                    <b-badge
                        title="Edit"
                        class="btn"
                        @click="view(row.item.id)"
                        pill
                        variant="primary"
                        ><b-icon icon="pencil-square"></b-icon
                    ></b-badge>
                    <b-badge
                        title="Hapus"
                        class="btn"
                        @click="remove(row.item.id)"
                        pill
                        variant="danger"
                        ><b-icon icon="trash"></b-icon
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
                <div>
                    <b-pagination
                        v-model="tableParams.page"
                        pills
                        :total-rows="collection.total"
                        :per-page="collection.per_page"
                        v-if="collection.data && collection.data.length > 0"
                    ></b-pagination>
                </div>
            </div>
        </b-card>

        <!-- Modal Add Project -->
        <b-modal
            id="modal-form-cost-category"
            size="md"
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
                { key: "name", label: "Nama", sortable: false },
                { key: "description", label: "Deskripsi", sortable: false },
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
            fileTitle: "Kategori Biaya"
        };
    },
    computed: {
        ...mapState("mstCostCategory", {
            collection: state => state.collection,
            isBusy: state => state.isBusy,
        }),
        tableParams: {
            get() {
                //MENGAMBIL VALUE PAGE DARI VUEX MODULE
                return this.$store.state.admin.mstCostCategory.tableParams;
            },
            set(params) {
                this.$store.commit("mstCostCategory/SET_TABLE_PARAMS", params);
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
        ...mapActions("mstCostCategory", [
            "load",
            "show",
            "store",
            "update",
            "destroy",
        ]),
        create() {
            this.$bvModal.show("modal-form-cost-category");
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
            this.$bvModal.hide("modal-form-cost-category");
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
        }
    }
};
</script>
