<template>
    <div>
        <b-row>
            <b-col cols="12">
                <detailComponent :modelId="modelId"/>
                <hr>
            </b-col>
            <b-col cols="12" class="text-center py-2">
                <div v-if="form.status != 2" class="form-group pb-2">
                    <label class="required">Catatan</label>
                    <b-form-textarea :class="{ 'has-error': errors.note }" v-model="form.note" placeholder="Alasan ditolak..." rows="3" max-rows="6" ></b-form-textarea>
                    <p class="text-danger" v-if="errors.note">Isi alasan terlebih dahulu.</p>
                </div>
                <div v-if="form.status == 2" class="form-group pb-2">
                    <label class="required">Upload Bukti Transfer</label>
                    <b-form-file @change="handleAttachment" name="file" ref="file" size="sm" accept=".jpg, .png, .jpeg"></b-form-file>
                    <p class="text-danger" v-if="errors.file">Mohon Untuk Upload Bukti Transfer Uang Muka Ke No Rekening Tertera</p>
                </div>
                <b-button-group>
                    <b-button @click="setStatus(true)" size="lg" variant="success"><b-icon icon="check-circle" v-if="form.status == 2"></b-icon> Setuju</b-button>
                    <b-button @click="setStatus(false)" size="lg" variant="danger"><b-icon icon="check-circle" v-if="form.status != 2"></b-icon> Tolak</b-button>
                </b-button-group>
            </b-col>
        </b-row>
    </div>
</template>
<script>
import detailComponent from "./detail.vue"
import { mapState, mapMutations, mapActions } from 'vuex'

export default {
    props:['modelId'],
    components:{
        detailComponent
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('downPaymentRequest', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('downPaymentRequest', ['CLEAR_FORM']),
        setStatus(status){
            if(status) this.form.status = 2
            else this.form.status = 4
        },
        handleAttachment(file){
            const reader = new FileReader();
            reader.onload = (event) => {
                this.form.file = event.target.result ?? null
            }
            reader.readAsDataURL(file.target.files[0])
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