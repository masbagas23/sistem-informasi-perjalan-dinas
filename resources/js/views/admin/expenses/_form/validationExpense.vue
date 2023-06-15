<template>
    <div>
        <b-table
            :fields="fields"
            show-empty
            bordered
            small
            breceptioned
            responsive="sm"
            class="py-2"
            :items="form.details"
        >
            <template v-slot:cell(index)="row">
                {{ row.index + 1 }}
            </template>

            <template v-slot:cell(status)="row">
                <b-button-group>
                    <b-button @click="setStatus(row.item, 2)" size="sm" variant="success"><b-icon icon="check-circle" v-if="row.value == 2"></b-icon> Valid</b-button>
                    <b-button @click="setStatus(row.item, 3)" size="sm" variant="danger"><b-icon icon="check-circle" v-if="row.value == 3"></b-icon> Tidak Valid</b-button>
                </b-button-group>
            </template>
            
            <template v-slot:cell(expense)="row">
                <b-table-simple borderless small>
                    <b-tr>
                        <b-td style="width:100px">Kategori</b-td>
                        <b-td style="width:10px">:</b-td>
                        <b-td>{{row.item.cost_category.name}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width:100px">Nominal</b-td>
                        <b-td style="width:10px">:</b-td>
                        <b-td>Rp {{formatCurrency(row.item.nominal)}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width:100px">Deskripsi</b-td>
                        <b-td style="width:10px">:</b-td>
                        <b-td>{{row.item.description}}</b-td>
                    </b-tr>
                </b-table-simple>
            </template>

            <template v-slot:cell(file_path)="row">
                <a @click="preview(row.value)" href="#"><div class="text-center"><b-img :src="row.value ? row.value : row.item.file" rounded class="pb-2" height="100px"></b-img></div></a>
            </template>
        </b-table>

        <b-modal
            id="modal-preview-image"
            size="lg"
            title="Preview"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
        >
            <b-col cols="12">
                <div class="text-center">
                    <b-img class="w-100" :src="imgUrl"></b-img>
                </div>
            </b-col>
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Kembali</b-button
                >
            </template>
        </b-modal>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from "vuex";
import {formatCurrency} from '@app/utils/formatter'
export default {
    props: ["form"],
    created() {
        this.loadCostCategory();
    },
    data() {
        return {
            imgUrl:'',
            fields: [
                {
                    key: "index",
                    label: "#",
                    thStyle: "text-align:center",
                    tdClass: "text-center"
                },
                {
                    key: "expense",
                    label: "Biaya Pengeluaran",
                    thStyle: "text-align:center"
                },
                {
                    key: "file_path",
                    label: "Bukti",
                    thStyle: "width:200px;text-align:center",
                },
                {
                    key: "status",
                    tdClass: "text-center",
                    thStyle: "text-align:center;width:250px",
                    label: "Status",
                },
            ]
        };
    },
    computed: {
        ...mapState(["errors"]), //MENGAMBIL STATE ERRORS
        ...mapState("mstCostCategory", {
            costCategories: state => state.collectionList, //MENGAMBIL STATE DATA
            isShow: state => state.isShow
        }),
        totalNominal(){
            let nominal = 0
            this.form.details.forEach(item => {
                nominal = nominal + item.nominal
            });
            return nominal
        },
    },
    methods: {
        ...mapActions("mstCostCategory", { loadCostCategory: "loadList" }),
        formatCurrency,
        addTarget(){
            this.form.details.push({ cost_category_id: '', nominal: '', description: '', file_path: '', file: '' })
        },
        handleAttachment(item, event){
            this.imgBase64(event.target.files[0], item)
        },
        imgBase64(file, item){
            const reader = new FileReader();
            reader.onload = (event) => {
                item.file = event.target.result ?? null
            }
            reader.readAsDataURL(file)
        },
        hideModal(loading) {
            this.imgUrl = ""
            this.$bvModal.hide("modal-preview-image");
        },
        preview(path){
            this.imgUrl = path
            this.$bvModal.show("modal-preview-image");

        },
        remove(index){
            this.form.details.splice(index,1)
        },
        setStatus(item, status){
            item.status = status
        }
    }
};
</script>
