<template>
    <div>
        <b-table
            :fields="filterFields"
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
                <div v-if="form.status == 1">
                    <b-button-group>
                        <b-button @click="setStatus(row.item, 2)" size="sm" variant="success"><b-icon icon="check-circle" v-if="row.value == 2"></b-icon> Valid</b-button>
                        <b-button @click="setStatus(row.item, 3)" size="sm" variant="danger"><b-icon icon="check-circle" v-if="row.value == 3"></b-icon> Tidak Valid</b-button>
                    </b-button-group>
                    <div class="text-center mt-2" v-if="row.value == 3">
                        <b-form-textarea v-model="row.item.reason" placeholder="Alasan tidak valid : contoh, bukti nota editan" rows="2" max-rows="6" ></b-form-textarea>
                        <p class="text-danger" v-if="errors.reason">{{errors.reason[0]}}</p>
                    </div>
                </div>
                <div v-else>
                    <b-badge
                        v-if="row.value == 2"
                        pill
                        variant="success"
                    >Valid</b-badge>
                    <b-badge
                        v-if="row.value == 3"
                        pill
                        variant="danger"
                    >Tidak Valid</b-badge>
                    <!-- <b>{{row.value == 2 ? 'Valid' : 'Tidak Valid'}}</b><br> -->
                    <br><em v-if="row.value == 3"><small>Alasan: {{row.item.reason}}</small></em>
                </div>
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
                        <b-td>
                            Rp {{formatCurrency(row.item.nominal)}}<br>
                            <span v-if="row.item.nominal > row.item.cost_category.max_price"><em><small class="text-danger">Nominal melebihi alokasi dana</small></em></span>
                        </b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width:100px">Qty</b-td>
                        <b-td style="width:10px">:</b-td>
                        <b-td>{{row.item.qty}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width:100px">Deskripsi</b-td>
                        <b-td style="width:10px">:</b-td>
                        <b-td>{{row.item.description}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width:100px">Total</b-td>
                        <b-td style="width:10px">:</b-td>
                        <b-td><b>Rp {{formatCurrency(row.item.total_nominal)}}</b></b-td>
                    </b-tr>
                </b-table-simple>
            </template>

            <template v-slot:cell(file_path)="row">
                <a v-if="row.value" @click="preview(row.value)" href="#"><div class="text-center"><b-img :src="row.value ? row.value : row.item.file" rounded class="pb-2" height="100px"></b-img></div></a>
            </template>
        </b-table>

        <b-modal
            id="modal-preview-image"
            size="lg"
            title="Preview"
            ref="modal"
            no-close-on-esc
            no-close-on-backdrop
            hide-header-close
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
    props: ["form","mode"],
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
                    // thStyle: "text-align:center"
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
                    // isDetail: true
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
        filterFields(){
            if(this.mode == 'detail'){
                return this.fields.filter(item => !item.isDetail)
            }else{
                return this.fields
            }
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
