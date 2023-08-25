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
                            <label class="required">Nama</label>
                            <input placeholder="Nama" type="text" :class="{ 'has-error': errors.name }" class="form-control" v-model="form.name">
                            <p class="text-danger" v-if="errors.name">{{ errors.name[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label class="">Alokasi Dana</label>
                            <money v-bind="money" placeholder="Nominal Uang Muka" :class="{ 'has-error': errors.max_price }" class="form-control" v-model="form.max_price"/>
                            <em><small>Digunakan sebagai acuan pada tahap validasi biaya pengeluaran</small></em>
                            <p class="text-danger" v-if="errors.max_price">{{ errors.max_price[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <b-form-textarea :class="{ 'has-error': errors.description }" v-model="form.description" placeholder="Penjelasan biaya..." rows="3" max-rows="6" ></b-form-textarea>
                            <p class="text-danger" v-if="errors.description">{{ errors.description[0] }}</p>
                        </div>
                    </b-col>
                </b-row>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import {Money} from 'v-money'

export default {
    props:['modelId'],
    created(){
        if(this.modelId > 0) this.show(this.modelId)
    },
    components: {Money},
    data(){
        return {
            money: {
                decimal: ',',
                thousands: '.',
                prefix: 'Rp ',
                precision: 0,
                masked: false
            },
        }
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('mstCostCategory', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('mstCostCategory', ['CLEAR_FORM']),
        ...mapActions('mstCostCategory', ['show']),
        
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>