<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div v-else>
                <b-row>
                    <b-col cols="5">
                        <div class="form-group">
                            <label class="required">Perjalan Dinas</label>
                            <v-select @input="handleApplication" class="boot-style" v-model="form.application_id" label="code" :reduce="item => item.id" :options="businessTrips.data">
                                <template #option="{ customer, start_date, code, end_date}">
                                    <p class="m-0">{{ code }} - {{customer.name}}</p>
                                    <small><em>{{ formatDate(start_date) }} - {{formatDate(end_date)}}</em></small>
                                </template>
                            </v-select>
                            <p class="text-danger" v-if="errors.application_id">{{ errors.application_id[0] }}</p>
                        </div>
                    </b-col>
                    <b-col cols="3">
                        <div class="form-group">
                            <label class="required">Kategori Kendaraan</label>
                            <v-select @input="handleVehicleCategory" class="boot-style" v-model="filterCategory" label="name" :reduce="item => item.id" :options="vehicleCategories">
                                
                            </v-select>
                        </div>
                    </b-col>
                    <b-col cols="4">
                        <div class="form-group">
                            <label class="required">Kendaraan</label>
                            <v-select class="boot-style" :disabled="!filterCategory" v-model="form.vehicle_id" label="name" :reduce="item => item.id" :options="vehicles.data"></v-select>
                            <p class="text-danger" v-if="errors.vehicle_id">{{ errors.vehicle_id[0] }}</p>
                        </div>
                    </b-col>
                    <!-- <b-col cols="12">
                        <div class="form-group">
                            <label>Catatan</label>
                            <b-form-textarea :class="{ 'has-error': errors.note }" v-model="form.note" placeholder="Penjelasan pekerjaan..." rows="3" max-rows="6" ></b-form-textarea>
                            <p class="text-danger" v-if="errors.note">{{ errors.note[0] }}</p>
                        </div>
                    </b-col> -->
                </b-row>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import {formatDate} from '@app/utils/formatter'

export default {
    props:['modelId'],
    created(){
        if(this.modelId > 0){
            this.show(this.modelId).then((r) => {
                this.filterCategory = r.data.vehicle.category
                this.form.start_date = r.data.application.start_date
                this.form.end_date = r.data.application.end_date
                this.form.total_day = r.data.application.total_day
            })
        }
        this.loadBusinessTrip({'status':'approved', 'result':'waiting'})
    },
    data(){
        return {
            filterCategory: ''
        }
    },
    computed: {
        ...mapState(['errors']), //MENGAMBIL STATE ERRORS
        ...mapState('vehicleLoan', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
        ...mapState('mstVehicle', {
            vehicles: state => state.collectionList,
            vehicleCategories: state => state.category,
        }),
        ...mapState('businessTripApplication', {
            businessTrips: state => state.collectionList,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('vehicleLoan', ['CLEAR_FORM']),
        ...mapActions('vehicleLoan', ['show']),
        ...mapActions('mstVehicle', {loadVehicle:'loadList'}),
        ...mapActions('businessTripApplication', {loadBusinessTrip:'loadList'}),
        formatDate,
        handleVehicleCategory(){
            this.loadVehicle({'category': this.filterCategory})
        },
        handleApplication(){
            const application = this.businessTrips.data.find(item => item.id == this.form.application_id)
            this.form.start_date = application.start_date
            this.form.end_date = application.end_date
            this.form.total_day = application.total_day
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