<template>
    <b-row>
        <b-col cols="12">
            <div class="form-group">
                <label class="required">Catatan</label>
                <b-form-textarea :class="{ 'has-error': errors.note }" v-model="form.note" placeholder="Alasan dibatalkan..." rows="3" max-rows="6" ></b-form-textarea>
                <p class="text-danger" v-if="errors.note">{{ errors.note[0] }}</p>
            </div>
        </b-col>
    </b-row>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'

export default {
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('vehicleLoan', {
            form: state => state.form, //MENGAMBIL STATE DATA
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('vehicleLoan', ['CLEAR_FORM']),
        
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
}
</script>