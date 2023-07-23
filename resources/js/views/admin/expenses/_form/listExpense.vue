<template>
    <div>
        <!-- sticky-header -->
        <div class="float-right mb-2">
            <button @click="addTarget" class="btn btn-sm btn-success"><b-icon icon="plus"></b-icon> Tambah</button>
        </div>
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

            <template v-slot:cell(cost_category_id)="row">
                <v-select class="boot-style" v-model="row.item.cost_category_id" label="name" :reduce="item => item.id" :options="costCategories.data"></v-select>
            </template>

            <template v-slot:cell(nominal)="row">
                <money v-bind="money" placeholder="Nominal Biaya" class="form-control" v-model="row.item.nominal"/>
            </template>

            <template v-slot:cell(description)="row">
                <b-form-textarea v-model="row.item.description" placeholder="Deskripsi Pengeluaran" rows="1" max-rows="2" ></b-form-textarea>
            </template>

            <template v-slot:cell(file_path)="row">
                <a v-if="row.value" @click="preview(row.value)" href="#"><div class="text-center"><b-img :src="row.value ? row.value : row.item.file" rounded class="pb-2" height="100px"></b-img></div></a>
                <b-form-file @change="handleAttachment(row.item, $event)" name="file[]" ref="file" size="sm" accept=".jpg, .png, .webp"></b-form-file>
            </template>
            <template v-slot:cell(action)>
                <b-badge
                    title="Hapus"
                    class="btn"
                    @click="remove(index)"
                    pill
                    variant="danger"
                    ><b-icon icon="trash"></b-icon
                ></b-badge>
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
import {Money} from 'v-money'

export default {
    props: ["form"],
    created() {
        this.loadCostCategory();
    },
    components:{
        Money
    },
    data() {
        return {
            imgUrl:'',
            money: {
                decimal: ',',
                thousands: '.',
                prefix: 'Rp ',
                precision: 0,
                masked: false
            },
            fields: [
                {
                    key: "index",
                    label: "#",
                    thStyle: "text-align:center",
                    tdClass: "text-center"
                },
                {
                    key: "cost_category_id",
                    label: "Kategori Biaya",
                    thStyle: "min-width:200px;text-align:center"
                },
                {
                    key: "nominal",
                    label: "Nominal",
                    thStyle: "text-align:center"
                },
                {
                    key: "description",
                    label: "Deskripsi"
                },
                {
                    key: "file_path",
                    label: "Bukti",
                    thStyle: "width:200px"
                },
                {
                    key: "action",
                    label: "Aksi",
                    tdClass: "text-center",
                    thStyle: "width:80px;text-align:center"
                }
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
        }
    }
};
</script>
