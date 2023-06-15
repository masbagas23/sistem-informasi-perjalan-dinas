<template>
    <div>
        <b-row>
            <b-col cols="12">
                <b-form-file @change="handleAttachment" name="file" ref="file" size="sm" accept=".jpg, .png, .webp"></b-form-file>
            </b-col>
        </b-row>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'

export default {
    computed:{
        ...mapState('expense', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods:{
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('expense', ['CLEAR_FORM']),
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