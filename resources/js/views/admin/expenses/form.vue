<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row v-if="form.application_id > 0">
                    <b-col cols="12">
                        <detailTripComponent :key="form.application_id" :modelId="form.application_id"/>
                    </b-col>
                </b-row>
                <b-row class="pt-2">
                    <b-col cols="6">
                        <div class="form-group">
                            <label class="required">Perjalan Dinas</label>
                            <v-select @input="handleApplication()" class="boot-style" v-model="form.application_id" label="code" :reduce="item => item.id" :options="businessTrips.data">
                                <template #option="{ customer, start_date, code, end_date}">
                                    <p class="m-0">{{ code }} - {{customer.name}}</p>
                                    <small><em>{{ formatDate(start_date) }} - {{formatDate(end_date)}}</em></small>
                                </template>
                            </v-select>
                            <p class="text-danger" v-if="errors.application_id">{{ errors.application_id[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="6">
                        <div class="form-group">
                            <label>Uang Muka</label>
                            <money v-bind="money" readonly placeholder="Nominal Uang Muka" class="form-control" v-model="form.down_payment"/>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <fieldset class="border px-2">
                            <legend  class="w-auto required"><span style="font-size:20px">Detail Biaya Pengeluaran</span></legend>
                            <b-col cols="12">
                                <listExpenseComponent :form="form"/>
                            </b-col>
                        </fieldset>
                    </b-col>
                </b-row>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import {formatDate} from '@app/utils/formatter'
import listExpenseComponent from "./_form/listExpense.vue"
import detailTripComponent from "@app/views/admin/business-trip-applications/detail.vue"
import {Money} from 'v-money'

export default {
    props:['modelId'],
    created(){
        if(this.modelId > 0) this.show(this.modelId)
        this.loadBusinessTrip({status:'approved', page:'expense'})
    },
    components:{
        listExpenseComponent,
        detailTripComponent,
        Money
    },
    data(){
        return{
            money: {
                decimal: ',',
                thousands: '.',
                prefix: 'Rp ',
                precision: 0,
                masked: false
            }
        }
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('expense', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
        ...mapState('businessTripApplication', {
            businessTrips: state => state.collectionList,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('expense', ['CLEAR_FORM']),
        ...mapActions('expense', ['show']),
        ...mapActions('businessTripApplication', {loadBusinessTrip:'loadList'}),
        ...mapActions('downPaymentRequest', {loadDownPayment:'loadList'}),
        formatDate,
        handleApplication(){
            if(this.form.application_id > 0){
                this.loadDownPayment({'filter_application': this.form.application_id, 'filter_status':'approved'}).then(r => {
                    if(r.data.length > 0){
                        this.form.down_payment = r.data[0].nominal
                    }
                })
            }else{
                this.form.down_payment = 0
            }
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