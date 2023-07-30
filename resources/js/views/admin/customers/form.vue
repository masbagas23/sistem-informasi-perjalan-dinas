<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="6">
                        <div class="form-group">
                            <label class="required">Nama</label>
                            <input placeholder="Nama" type="text" :class="{ 'has-error': errors.name }" class="form-control" v-model="form.name">
                            <p class="text-danger" v-if="errors.name">Wajib isi nama</p>
                        </div>
                    </b-col>
                    <b-col cols="6">
                        <div class="form-group">
                            <label class="required">Telepon</label>
                                <input placeholder="Telepon" type="number" :class="{ 'has-error': errors.telephone }" class="form-control" v-model="form.telephone">
                            <p class="text-danger" v-if="errors.telephone">Wajib isi nomor telepon</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label>Link Google Maps</label>
                                <input placeholder="Paste Link Google Maps" type="text" :class="{ 'has-error': errors.gmaps_url }" class="form-control" v-model="form.gmaps_url">                                
                            <p class="text-danger" v-if="errors.gmaps_url">{{ errors.gmaps_url[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label class="required">Alamat</label>
                            <b-form-textarea :class="{ 'has-error': errors.address }" v-model="form.address" placeholder="Alamat lengkap pelanggan..." rows="3" max-rows="6" ></b-form-textarea>
                            <p class="text-danger" v-if="errors.address">Wajib isi alamat</p>
                        </div>
                    </b-col>
                </b-row>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'

export default {
    props:['modelId'],
    created(){
        if(this.modelId > 0) this.show(this.modelId)
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('mstCustomer', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('mstCustomer', ['CLEAR_FORM']),
        ...mapActions('mstCustomer', ['show']),
        
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>