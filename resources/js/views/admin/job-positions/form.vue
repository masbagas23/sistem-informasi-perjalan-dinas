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
                            <label class="required">Hak Akses</label>
                            <v-select class="boot-style" v-model="form.role_id" label="name" :reduce="item => item.id" :options="roles.data"></v-select>
                            <p class="text-danger" v-if="errors.role_id">{{ errors.role_id[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <b-form-textarea :class="{ 'has-error': errors.description }" v-model="form.description" placeholder="Penjelasan jabatan..." rows="3" max-rows="6" ></b-form-textarea>
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
        if(this.modelId > 0) this.show(this.modelId)
        this.loadRole()
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('mstJobPosition', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
        ...mapState('mstRole', {
            roles: state => state.collectionList, //MENGAMBIL STATE DATA
            roleLoading: state => state.isLoading,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('mstJobPosition', ['CLEAR_FORM']),
        ...mapActions('mstJobPosition', ['show']),
        ...mapActions('mstRole', {loadRole:'loadList'}),
        
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>