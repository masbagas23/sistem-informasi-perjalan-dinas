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
                                <b-td>Kendaraaan</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{form.vehicle.name}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>Plat Nomor</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{form.vehicle.number_plate_license}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>Tanggal</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{formatDate(form.start_date)}} - {{formatDate(form.end_date)}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>Pemohon</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{form.requester.first_name}} {{form.requester.middle_name}} {{form.requester.last_name}}</b-td>
                            </b-tr>
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
                                <b-td>Catatan</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{form.note}}</b-td>
                            </b-tr>
                        </b-table-simple>
                    </b-col>
                </b-row>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import {formatDate} from '@app/utils/formatter'

export default {
    props:['modelId'],
    created(){
        this.show(this.modelId)
    },
    computed:{
        ...mapState('vehicleLoan', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods:{
        ...mapMutations('vehicleLoan', ['CLEAR_FORM']),
        ...mapActions('vehicleLoan', ['show']),
        formatDate
    }
}
</script>