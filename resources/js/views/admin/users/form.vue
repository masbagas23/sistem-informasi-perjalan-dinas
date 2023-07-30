<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="4 text-center">
                        <b-img :src="form.avatar_url ? form.avatar_url : '/images/ava.webp'" rounded alt="Foto Profil" class="pb-2" width="170px"></b-img>
                        <b-form-file id="file" ref="file" @change="handleFileUpload" size="sm" accept=".jpg, .png, .jpeg"></b-form-file>
                    </b-col>
                    <b-col cols="8">
                        <b-row>
                            <b-col cols="4">
                                <div class="form-group">
                                    <label class="required">NIP</label>
                                    <input placeholder="NIP" type="text" :class="{ 'has-error': errors.nip }" class="form-control" v-model="form.nip">
                                    <p class="text-danger" v-if="errors.nip">Wajib isi NIP</p>
                                </div>
                            </b-col>
                            <b-col cols="8">
                                <div class="form-group">
                                    <label class="required">Jabatan</label>
                                    <v-select class="boot-style" v-model="form.job_position_id" label="name" :reduce="item => item.id" :options="jobPositions.data"></v-select>
                                    <p class="text-danger" v-if="errors.nip">Wajib isi jabatan</p>
                                </div>
                            </b-col>
                            <b-col cols="4">
                                <div class="form-group">
                                    <label class="required">Nama Depan</label>
                                    <input placeholder="Nama Depan" type="text" :class="{ 'has-error': errors.first_name }" class="form-control" v-model="form.first_name">
                                    <p class="text-danger" v-if="errors.first_name">Wajib isi nama depan</p>
                                </div>
                            </b-col>
                            <b-col cols="4">
                                <div class="form-group">
                                    <label class="">Nama Tengah</label>
                                    <input placeholder="Nama Tengah" type="text" :class="{ 'has-error': errors.middle_name }" class="form-control" v-model="form.middle_name">
                                    <p class="text-danger" v-if="errors.middle_name">{{ errors.middle_name[0] }}</p>
                                </div>
                            </b-col>
                            <b-col cols="4">
                                <div class="form-group">
                                    <label class="required">Nama Belakang</label>
                                    <input placeholder="Nama Belakang" type="text" :class="{ 'has-error': errors.last_name }" class="form-control" v-model="form.last_name">
                                    <p class="text-danger" v-if="errors.last_name">Wajib isi nama belakang</p>
                                </div>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <hr>
                <b-row>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Email</label>
                            <input placeholder="Email" type="email" :class="{ 'has-error': errors.email }" class="form-control" v-model="form.email">
                            <p class="text-danger" v-if="errors.email">Wajib isi email</p>
                        </div>
                    </b-col>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Nomor HP</label>
                            <input placeholder="Nomor HP" type="number" :class="{ 'has-error': errors.phone_number }" class="form-control" v-model="form.phone_number">
                            <p class="text-danger" v-if="errors.phone_number">Wajib isi nomor hp</p>
                        </div>
                    </b-col>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Jenis Kelamin</label>
                            <v-select class="boot-style" v-model="form.gender" label="name" :reduce="item => item.id" :options="genders"></v-select>
                            <p class="text-danger" v-if="errors.gender">Wajib isi jenis kelamin</p>
                        </div>
                    </b-col>
                    <b-col cols="6">
                        <div class="form-group">
                            <label class="required">No Rekening Bank</label>
                            <input placeholder="Nomor Rekening Bank Gajian" type="number" :class="{ 'has-error': errors.bank_number }" class="form-control" v-model="form.bank_number">
                            <p class="text-danger" v-if="errors.bank_number">Wajib isi no rekening bank</p>
                        </div>
                    </b-col>
                    <b-col cols="6">
                        <div class="form-group">
                            <label class="">Tanda Tangan</label>
                            <b-form-file @change="handleSignature" accept=".jpg, .png, .jpeg"></b-form-file>
                            <p class="text-danger" v-if="errors.signature">Wajib upload tanda tangan</p>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div class="form-group">
                            <label>Alamat</label>
                            <b-form-textarea :class="{ 'has-error': errors.address }" v-model="form.address" placeholder="Alamat lengkap..." rows="3" max-rows="6" ></b-form-textarea>
                            <p class="text-danger" v-if="errors.address">{{ errors.address[0] }}</p>
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
        this.loadJobPosition()
    },
    data(){
        return{
            genders:[
                {id:1, name:"Laki-Laki"},
                {id:2, name:"Perempuan"},
            ]
        }
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('mstUser', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
        ...mapState('mstJobPosition', {
            jobPositions: state => state.collectionList, //MENGAMBIL STATE DATA
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('mstUser', ['CLEAR_FORM']),
        ...mapActions('mstUser', ['show']),
        ...mapActions('mstJobPosition', {loadJobPosition:'loadList'}),
        handleFileUpload(file){
            this.form.file = file.target.files[0];
        },
        handleSignature(file){
            this.form.signature = file.target.files[0];
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