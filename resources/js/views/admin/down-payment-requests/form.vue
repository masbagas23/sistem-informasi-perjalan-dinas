<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="12">
                        <div class="form-group">
                            <label class="required">Perjalan Dinas</label>
                            <v-select class="boot-style" v-model="form.application_id" label="code" :reduce="item => item.id" :options="businessTrips.data">
                                <template #option="{ customer, start_date, code, end_date}">
                                    <p class="m-0">{{ code }} - {{customer.name}}</p>
                                    <small><em>{{ formatDate(start_date) }} - {{formatDate(end_date)}}</em></small>
                                </template>
                            </v-select>
                            <p class="text-danger" v-if="errors.application_id">{{ errors.application_id[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label class="required">Nominal</label>
                            <money @input="handleNominal" v-bind="money" placeholder="Nominal Uang Muka" :class="{ 'has-error': errors.nominal }" class="form-control" v-model="form.nominal"/>
                            <p class="text-danger" v-if="errors.nominal">{{ errors.nominal[0] }}</p>
                            <p class="text-danger" v-if="errNominal">{{ errNominal }}</p>
                        </div>
                    </b-col>
                </b-row>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import {formatDate} from '@app/utils/formatter'
import {Money} from 'v-money'

export default {
    props:['modelId'],
    created(){
        if(this.modelId > 0) this.show(this.modelId)
        this.loadBusinessTrip({status:'approved', result:'waiting', page:'down_payment'})
    },
    components: {Money},
    data(){
        return{
            money: {
                decimal: ',',
                thousands: '.',
                prefix: 'Rp ',
                precision: 0,
                masked: false
            },
            errNominal:'',
        }
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('downPaymentRequest', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
        ...mapState('businessTripApplication', {
            businessTrips: state => state.collectionList,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('downPaymentRequest', ['CLEAR_FORM']),
        ...mapActions('downPaymentRequest', ['show']),
        ...mapActions('businessTripApplication', {loadBusinessTrip:'loadList'}),
        formatDate,
        handleNominal(nominal){
            if(nominal < 50000){
                this.errNominal = "Minimal pengajuan sebesar Rp 50.000"
                this.$emit('toogleSubmit', false)
            }else if(nominal > 1000000){
                this.errNominal = "Maksimal pengajuan sebesar Rp 1.000.000"
                this.$emit('toogleSubmit', false)
            }else{
                this.errNominal = ''
                this.$emit('toogleSubmit', true)
            }
        },
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>