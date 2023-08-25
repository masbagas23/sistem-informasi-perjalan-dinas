<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col v-if="form.status_reimburse == 2" cols="12" >
                        <img :src="form.reimburse_path" class="w-100">
                    </b-col>
                    <b-col v-else cols="12">
                        <b-alert variant="primary" class="text-center" show>
                            <h4>No Rekening</h4>
                            {{requester.bank_number}} a.n {{requester.first_name}} {{requester.middle_name}} {{requester.last_name}}
                            <b><p>Bank BNI</p></b>
                        </b-alert>
                        <b-form-file @change="handleAttachment" name="file" ref="file" size="sm" accept=".jpg, .png, .jpeg"></b-form-file>
                        <p class="text-danger" v-if="errors.reimburse_file">Wajib upload bukti transfer</p>
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
            this.show(this.modelId).then(e => {
                this.requester = e.data.application.down_payment.requester
            })
        }
    },
    data(){
        return {
            requester:''
        }
    },
    computed:{
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('expense', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods:{
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('expense', ['CLEAR_FORM']),
        ...mapActions('expense', ['show']),
        handleAttachment(file){
            const reader = new FileReader();
            reader.onload = (event) => {
                this.form.reimburse_file = event.target.result ?? null
            }
            reader.readAsDataURL(file.target.files[0])
        },
    }
}
</script>