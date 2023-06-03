<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="4 text-center">
                        <b-img :src="form.ava_url ? form.ava_url : '/images/ava.webp'" rounded alt="Foto Profil" class="w-75"></b-img>
                    </b-col>
                    <b-col cols="2">
                        <div class="form-group">
                            <label class="required">NIP</label>
                            <input placeholder="NIP" type="text" :class="{ 'has-error': errors.nip }" class="form-control" v-model="form.nip">
                            <p class="text-danger" v-if="errors.nip">{{ errors.nip[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="2">
                        <div class="form-group">
                            <label class="required">NIP</label>
                            <vSelect label="name" :options="jobPositions"></vSelect>
                            <p class="text-danger" v-if="errors.nip">{{ errors.nip[0] }}</p>
                        </div>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Nama Depan</label>
                            <input placeholder="Nama Depan" type="text" :class="{ 'has-error': errors.first_name }" class="form-control" v-model="form.first_name">
                            <p class="text-danger" v-if="errors.first_name">{{ errors.first_name[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Nama Tengah</label>
                            <input placeholder="Nama Tengah" type="text" :class="{ 'has-error': errors.middle_name }" class="form-control" v-model="form.middle_name">
                            <p class="text-danger" v-if="errors.middle_name">{{ errors.middle_name[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Nama Belakang</label>
                            <input placeholder="Nama Belakang" type="text" :class="{ 'has-error': errors.last_name }" class="form-control" v-model="form.last_name">
                            <p class="text-danger" v-if="errors.last_name">{{ errors.last_name[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Email</label>
                            <input placeholder="Email" type="email" :class="{ 'has-error': errors.email }" class="form-control" v-model="form.email">
                            <p class="text-danger" v-if="errors.email">{{ errors.email[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Nomor HP</label>
                            <input placeholder="Nomor HP" type="number" :class="{ 'has-error': errors.phone_number }" class="form-control" v-model="form.phone_number">
                            <p class="text-danger" v-if="errors.phone_number">{{ errors.phone_number[0] }}</p>
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
        ...mapState('mstUser', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('mstUser', ['CLEAR_FORM']),
        ...mapActions('mstUser', ['show']),
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>