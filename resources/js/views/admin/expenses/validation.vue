<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="12">
                        <simpleDetailComponent :modelId="form.application_id"/>
                    </b-col>
                    <b-col cols="12">
                        <fieldset class="border px-2">
                            <legend  class="w-auto required"><span style="font-size:20px">Detail Biaya Pengeluaran</span></legend>
                            <b-col cols="12">
                                <listExpenseComponent :mode="mode" :form="form"/>
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
import listExpenseComponent from "./_form/validationExpense.vue"
import simpleDetailComponent from '../business-trip-applications/detail.vue'
export default {
    props:['modelId','mode'],
    created(){
        if(this.modelId > 0) this.show(this.modelId)
        this.loadBusinessTrip()
    },
    components:{
        listExpenseComponent,
        simpleDetailComponent
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