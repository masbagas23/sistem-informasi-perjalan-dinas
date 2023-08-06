<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="12">
                        <b-table-simple borderless small>
                            <b-tr>
                                <b-td>Kode</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{form.code}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>Nominal</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>Rp {{formatCurrency(form.nominal)}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>Tanggal Permintaan</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{formatDate(form.created_at)}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>Pemohon</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{form.requester.first_name}} {{form.requester.middle_name}} {{form.requester.last_name}}</b-td>
                            </b-tr>
                            <!-- <b-tr>
                                <b-td>No Rek Bank</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{form.requester.bank_number}}</b-td>
                            </b-tr> -->
                            <b-tr>
                                <b-td>Status</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>
                                    <b-badge v-if="form.status == 1" pill variant="warning">Menunggu</b-badge>
                                    <b-badge v-if="form.status == 2" pill variant="success">Disetujui</b-badge>
                                    <b-badge v-if="form.status == 3" pill variant="secondary">Dibatalkan</b-badge>
                                    <b-badge v-if="form.status == 4" pill variant="danger">Ditolak</b-badge>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>Alasan</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{form.note ? form.note : "-"}}</b-td>
                            </b-tr>
                            <b-tr v-show="form.status == 2">
                                <b-td>Bukti Transfer</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>
                                      <b-badge href="#" @click="previewImage" variant="secondary">Lihat Bukti</b-badge>
                                </b-td>
                            </b-tr>
                        </b-table-simple>
                    </b-col>
                </b-row>
                <b-alert v-show="form.status == 2 && form.file_path == null" variant="primary" class="text-center" show>
                    <h4>No Rekening</h4>
                    {{form.requester.bank_number}} a.n {{form.requester.first_name}} {{form.requester.middle_name}} {{form.requester.last_name}}
                </b-alert>
            </div>
        </b-overlay>
        <!-- Modal Detail -->
        <b-modal
            id="modal-preview-down-payment"
            size="lg"
            title="Pratinjau"
            ref="modal"
        >
            <img :src="form.file_path" class="w-100" alt="" srcset="">
            <template v-slot:modal-footer>
                <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                    ><i class="fas fa-arrow-left mr-2"></i>Kembali</b-button
                >
            </template>
        </b-modal>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import {formatDate, formatCurrency} from '@app/utils/formatter'

export default {
    props:['modelId'],
    created(){
        this.show(this.modelId)
    },
    computed:{
        ...mapState('downPaymentRequest', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods:{
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('downPaymentRequest', ['CLEAR_FORM']),
        ...mapActions('downPaymentRequest', ['show']),
        formatDate,formatCurrency,
        hideModal(){
            this.$bvModal.hide("modal-preview-down-payment");
        },
        previewImage(){
            this.$bvModal.show("modal-preview-down-payment");
        }
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>