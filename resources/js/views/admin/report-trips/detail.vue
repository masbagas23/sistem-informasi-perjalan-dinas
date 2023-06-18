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
                    :items="form.targets"
                >
                    <template v-slot:cell(index)="row">
                        {{ row.index + 1 }}
                    </template>

                    <template v-slot:cell(name)="row">
                        <b-table-simple borderless small>
                            <b-tr>
                                <b-td style="width:150px">Pekerjaan</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{row.value}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td style="width:150px">Selesai Pada</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{formatDate(row.item.start_date)}} - {{formatDate(row.item.end_date)}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td style="width:150px">Catatan</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{row.item.reason ? row.item.reason : '-'}}</b-td>
                            </b-tr>
                        </b-table-simple>
                    </template>

                    <template v-slot:cell(status)="row">
                        <b-badge v-if="row.value == 2" pill variant="success">Selesai</b-badge>
                        <b-badge v-if="row.value == 3" pill variant="secondary">Tertunda</b-badge>
                    </template>

                    <template v-slot:cell(file_path)="row">
                        <a @click="preview(row.value)" href="#"><div class="text-center"><b-img v-if="row.value" :src="row.value" rounded class="pb-2" height="100px"></b-img></div></a>
                    </template>
                </b-table>

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
import {formatDate} from "@app/utils/formatter"

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
                    key: "name",
                    label: "Pekerjaan"
                },
                {
                    key: "file_path",
                    label: "Dokumentasi",
                    thStyle: "width:200px;text-align:center"

                },
                {
                    key: "status",
                    label: "Status",
                    thStyle: "text-align:center;width:150px",
                    tdClass: "text-center"
                }
            ],
            statuses:[
                {id:1, name:'Menunggu'},
                {id:2, name:'Selesai'},
                {id:3, name:'Tertunda'},
                {id:4, name:'Jadwal Ulang'}
            ],
            imgUrl:'',
        };
    },
    computed: {
        ...mapState(["errors"]), //MENGAMBIL STATE ERRORS
        ...mapState('businessTripApplication', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('businessTripApplication', ['CLEAR_FORM']),
        ...mapActions('businessTripApplication', ['show']),
        formatDate,
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
