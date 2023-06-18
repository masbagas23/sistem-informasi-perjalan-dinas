<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div else>
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
                <b-alert show variant="info" class="text-center">
                    <b>Total Pengeluaran : Rp {{formatCurrency(form.total_nominal)}}</b>
                </b-alert>
                <b-modal
                    id="modal-preview-image-target"
                    size="md"
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
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from "vuex";
import {formatDate, formatCurrency} from "@app/utils/formatter"

export default {
    props: ["modelId"],
    created(){
        if(this.modelId > 0) this.show(this.modelId)
    },
    data() {
        return {
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
            ],
            imgUrl:'',
        };
    },
    computed: {
        ...mapState(["errors"]), //MENGAMBIL STATE ERRORS
        ...mapState('reportExpense', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('reportExpense', ['CLEAR_FORM']),
        ...mapActions('reportExpense', ['show']),
        formatDate,formatCurrency,
        preview(path){
            this.imgUrl = path
            this.$bvModal.show("modal-preview-image-target");
        },
        hideModal() {
            this.imgUrl = ""
            this.$bvModal.hide("modal-preview-image-target");
        },
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
};
</script>
