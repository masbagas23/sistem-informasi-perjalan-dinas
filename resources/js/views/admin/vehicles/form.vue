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
                            <p class="text-danger" v-if="errors.name">{{ errors.name[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="6">
                        <div class="form-group">
                            <label class="required">Kategori</label>
                                <v-select class="boot-style" v-model="form.category" label="name" :reduce="item => item.id" :options="categories"></v-select>
                            <p class="text-danger" v-if="errors.category">{{ errors.category[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label>Plat Nomor</label>
                            <input placeholder="Plat Nomor" type="text" :class="{ 'has-error': errors.number_plate_license }" class="form-control" v-model="form.number_plate_license">
                            <p class="text-danger" v-if="errors.number_plate_license">{{ errors.number_plate_license[0] }}</p>
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
    data(){
        return{
            categories :[
                {id:1, name:"Motor"},
                {id:2, name:"Mobil"},
            ]
        }
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('mstVehicle', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('mstVehicle', ['CLEAR_FORM']),
        ...mapActions('mstVehicle', ['show']),
        
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>