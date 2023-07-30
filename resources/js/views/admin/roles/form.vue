<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Kode</label>
                            <input placeholder="Kode" type="text" :class="{ 'has-error': errors.code }" class="form-control" v-model="form.code">
                            <p class="text-danger" v-if="errors.code">Wajib isi kode</p>
                        </div>
                    </b-col>
                    <b-col cols="8">
                        <div class="form-group">
                            <label class="required">Nama</label>
                            <input placeholder="Nama" type="text" :class="{ 'has-error': errors.name }" class="form-control" v-model="form.name">
                            <p class="text-danger" v-if="errors.name">Wajib isi nama</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <b-form-textarea :class="{ 'has-error': errors.description }" v-model="form.description" placeholder="Penjelasan hak akses..." rows="3" max-rows="6" ></b-form-textarea>
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

export default {
    props:['modelId'],
    created(){
        if(this.modelId > 0){
            this.show(this.modelId)
        }
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('mstRole', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('mstRole', ['CLEAR_FORM']),
        ...mapActions('mstRole', ['show']),
        
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>