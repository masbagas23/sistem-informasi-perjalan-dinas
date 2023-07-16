<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-alert class="text-center" v-if="form.status == 4" variant="warning" show dismissible>
                    <h4>Alasan Ditolak</h4>
                    {{form.note}}
                </b-alert>
                <b-row>
                    <!-- Tujuan -->
                    <b-col cols="12">
                        <fieldset class="border px-3">
                            <legend class="w-auto"><span style="font-size:20px">Tujuan</span></legend>
                            <b-row style="max-height:300px">
                                <b-col cols="6">
                                    <div class="form-group">
                                        <label class="required">Pelanggan</label>
                                        <v-select
                                            class="boot-style"
                                            v-model="form.customer_id"
                                            label="name"
                                            :reduce="item => item.id"
                                            :options="customers.data"
                                            placeholder="Pilih Tujuan"
                                        />
                                        <p class="text-danger" v-if="errors.customer_id">{{ errors.customer_id[0] }}</p>
                                    </div>
                                </b-col>
                                <b-col cols="6">
                                    <div class="form-group">
                                        <label class="required">Kategori Pekerjaan</label>
                                        <v-select
                                            class="boot-style"
                                            v-model="form.job_category_id"
                                            label="name"
                                            :reduce="item => item.id"
                                            :options="jobCategories.data"
                                            placeholder="Pilih Pekerjaan"
                                        />
                                        <p class="text-danger" v-if="errors.name">{{ errors.name[0] }}</p>
                                    </div>
                                </b-col>
                                <b-col cols="5">
                                    <div class="form-group">
                                        <label class="required">Tanggal Mulai</label>
                                        <b-form-datepicker placeholder="Pilih Tanggal Mulai" @input="changeStartDate" v-model="form.start_date" :min="today" locale="id"></b-form-datepicker>
                                        <p class="text-danger" v-if="errors.start_date">{{ errors.start_date[0] }}</p>
                                    </div>
                                </b-col>
                                <b-col cols="5">
                                    <div class="form-group">
                                        <label class="required">Tanggal Selesai</label>
                                        <b-form-datepicker placeholder="Pilih Tanggal Selesai" @input="changeEndDate" :disabled="!form.start_date" v-model="form.end_date" :min="startDate" locale="id"></b-form-datepicker>
                                        <p class="text-danger" v-if="errors.end_date">{{ errors.end_date[0] }}</p>
                                    </div>
                                </b-col>
                                <b-col cols="2">
                                    <div class="form-group">
                                        <label class="">Total Hari</label>
                                        <input readonly placeholder="0" type="number" class="form-control" v-model="form.total_day">
                                    </div>
                                </b-col>
                                <b-col cols="12">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <b-form-textarea :class="{ 'has-error': errors.description }" v-model="form.description" placeholder="Contoh : perbaikan perangkat NB-IoT" rows="3" max-rows="6" ></b-form-textarea>
                                        <p class="text-danger" v-if="errors.description">{{ errors.description[0] }}</p>
                                    </div>
                                </b-col>
                            </b-row>
                        </fieldset>
                    </b-col>
                    <!-- Peserta -->
                    <b-col v-if="form.start_date" cols="6">
                        <fieldset class="border px-3">
                            <legend  class="w-auto required"><span style="font-size:20px">Peserta</span></legend>
                            <b-row style="max-height:300px">
                                <b-col cols="12">
                                    <chooseUser :index="form.start_date" :form="form"/>
                                </b-col>
                            </b-row>
                        </fieldset>
                    </b-col>
                    <!-- Detail Pekerjaan -->
                    <b-col v-if="form.start_date" cols="6">
                        <fieldset class="border px-3">
                            <legend  class="w-auto required"><span style="font-size:20px">Daftar Tugas</span></legend>
                            <b-row style="max-height:300px">
                                <b-col cols="12">
                                    <listTarget :index="form.start_date" :form="form"/>
                                </b-col>
                            </b-row>
                        </fieldset>
                    </b-col>
                </b-row>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import chooseUser from './_form/chooseUser.vue'
import listTarget from './_form/listTarget.vue'

export default {
    props:['modelId'],
    created(){
        if(this.modelId > 0) this.show(this.modelId)
        this.loadCustomer()
        this.loadJobCategory()
    },
    components:{
        chooseUser,
        listTarget
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('businessTripApplication', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
        ...mapState('mstCustomer', {
            customers: state => state.collectionList,
        }),
        ...mapState('mstJobCategory', {
            jobCategories: state => state.collectionList,
        }),
        today(){
            const now = new Date()
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
            return new Date(today)

        },
        startDate(){
            const data = this.form.start_date ? new Date(this.form.start_date) : new Date()
            const date = new Date(data.getFullYear(), data.getMonth(), data.getDate())
            return new Date(date)
        },
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('businessTripApplication', ['CLEAR_FORM']),
        ...mapActions('businessTripApplication', ['show']),
        ...mapActions('mstCustomer', {loadCustomer:'loadList'}),
        ...mapActions('mstJobCategory', {loadJobCategory:'loadList'}),
        changeStartDate(date){
            this.form.end_date = date
            this.calculateDay()
        },
        changeEndDate(date){
            this.form.end_date = date
            this.calculateDay()
        },
        calculateDay(){
            if(this.form.start_date == '' && this.form.end_date == '') return 0
            const startDate = new Date(this.form.start_date)
            const endDate = new Date(this.form.end_date)
            this.form.total_day = Math.ceil(Math.abs(endDate - startDate) / (1000 * 60 * 60 * 24)) + 1
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