<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="12">
                        <fieldset class="border px-3">
                            <legend class="w-auto"><span style="font-size:20px">Informasi Perjalan Dinas</span></legend>
                            <b-row style="height:345px;max-height:345px">
                                <b-col cols="12">
                                    <b-table-simple borderless small>
                                        <tr>
                                            <td>Kode</td>
                                            <td style="width:10px">:</td>
                                            <td>{{form.code}}</td>
                                        </tr>
                                        <tr>
                                            <td>Nomor Surat</td>
                                            <td style="width:10px">:</td>
                                            <td>{{form.code_letter}}</td>
                                        </tr>
                                        <tr>
                                            <td>Pemohon</td>
                                            <td style="width:10px">:</td>
                                            <td>{{form.requester}}</td>
                                        </tr>
                                        <tr>
                                            <td>Tujuan</td>
                                            <td style="width:10px">:</td>
                                            <td>{{form.customer}}</td>
                                        </tr>
                                        <tr>
                                            <td style="width:150px">Kategori Pekerjaan</td>
                                            <td style="width:10px">:</td>
                                            <td>{{form.job_category}}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td style="width:10px">:</td>
                                            <td>{{formatDate(form.start_date)}} - {{formatDate(form.end_date)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td style="width:10px">:</td>
                                            <td>
                                                <b-badge v-if="form.status == 1" pill variant="warning">Menunggu</b-badge>
                                                <b-badge v-if="form.status == 2" pill variant="success">Disetujui</b-badge>
                                                <b-badge v-if="form.status == 3" pill variant="secondary">Dibatalkan</b-badge>
                                                <b-badge v-if="form.status == 4" pill variant="danger">Ditolak</b-badge>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Deskripsi</td>
                                            <td style="width:10px">:</td>
                                            <td>{{form.description}}</td>
                                        </tr>
                                        <tr>
                                            <td>Catatan</td>
                                            <td style="width:10px">:</td>
                                            <td>{{form.note}}</td>
                                        </tr>
                                    </b-table-simple>
                                </b-col>
                            </b-row>
                        </fieldset>
                    </b-col>                
                </b-row>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import { formatDate } from "@app/utils/formatter"

export default {
    props:['modelId'],
    created(){
        this.show(this.modelId)
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
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
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>