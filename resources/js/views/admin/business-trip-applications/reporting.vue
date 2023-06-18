<template>
    <div>
        <b-overlay :show="isShow" rounded="sm">
            <div v-if="isShow">
                <b-skeleton-table :rows="5" :columns="1" :table-props="{ bordered: true, striped: true }"></b-skeleton-table>
            </div>
            <div else>
                <b-table
                    :fields="fields"
                    show-empty
                    bordered
                    small
                    breceptioned
                    responsive="sm"
                    class="py-2"
                    :items="form.targets"
                >
                    <template v-slot:cell(index)="row">
                        {{ row.index + 1 }}
                    </template>
                    
                    <template v-slot:cell(date)="row">
                        <b-row>
                            <b-col cols="12">
                                <b-form-datepicker size="sm" placeholder="Tanggal Mulai" @input="changeStartDate" v-model="row.item.start_date" :min="form.start_date" :max="form.end_date" locale="id"></b-form-datepicker>
                            </b-col>
                            <b-col cols="12" class="text-center">
                                -
                            </b-col>
                            <b-col cols="12">
                                <b-form-datepicker size="sm" placeholder="Tanggal Selesai" @input="changeEndDate" :disabled="!form.start_date" v-model="row.item.end_date" :min="form.start_date" :max="form.end_date" locale="id"></b-form-datepicker>
                            </b-col>
                        </b-row>
                    </template>

                    <template v-slot:cell(name)="row">
                        <b-table-simple borderless small>
                            <b-tr>
                                <b-td style="width:150px">Pekerjaan</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{row.value}}</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td style="width:150px">Catatan</b-td>
                                <b-td style="width:10px">:</b-td>
                                <b-td>{{row.item.description}}</b-td>
                            </b-tr>
                        </b-table-simple>
                    </template>

                    <template v-slot:cell(status)="row">
                        <v-select
                            class="boot-style"
                            v-model="row.item.status"
                            label="name"
                            :reduce="item => item.id"
                            :options="statuses"
                            placeholder="Pilih Status"
                            :clearable="false"
                            :selectable="(option) => option.id != 1"
                        ></v-select>
                    </template>

                    <template v-slot:cell(file_path)="row">
                        <a @click="preview(row.value)" href="#"><div class="text-center"><b-img v-if="row.value" :src="row.value" rounded class="pb-2" height="100px"></b-img></div></a>
                        <b-form-file @change="handleAttachment(row.item, $event)" name="file[]" ref="file" size="sm" accept=".jpg, .png, .webp"></b-form-file>
                    </template>
                </b-table>

                <b-modal
                    id="modal-preview-image-target"
                    size="md"
                    title="Preview"
                    ref="modal"
                    no-close-on-esc
                    no-close-on-backdrop
                >
                    <b-col cols="12">
                        <div class="text-center">
                            <b-img class="w-100" :src="imgUrl"></b-img>
                        </div>
                    </b-col>
                    <template v-slot:modal-footer>
                        <b-button class="btn btn-secondary ml-2" @click="hideModal()"
                            ><i class="fas fa-arrow-left mr-2"></i>Kembali</b-button
                        >
                    </template>
                </b-modal>
            </div>
        </b-overlay>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
    props: ["modelId"],
    created(){
        if(this.modelId > 0) this.show(this.modelId)
    },
    data() {
        return {
            fields: [
                {
                    key: "index",
                    label: "#",
                    thStyle: "text-align:center",
                    tdClass: "text-center"
                },
                {
                    key: "name",
                    label: "Pekerjaan"
                },
                {
                    key: "date",
                    label: "Tanggal",
                    thStyle: "width:200px"
                },
                {
                    key: "file_path",
                    label: "Dokumentasi",
                    thStyle: "width:200px"

                },
                {
                    key: "status",
                    label: "Status",
                    thStyle: "text-align:center;width:250px"
                }
            ],
            statuses:[
                {id:1, name:'Menunggu'},
                {id:2, name:'Selesai'},
                {id:3, name:'Tertunda'},
                {id:4, name:'Jadwal Ulang'}
            ],
            imgUrl:'',
        };
    },
    computed: {
        ...mapState(["errors"]), //MENGAMBIL STATE ERRORS
        ...mapState('businessTripApplication', {
            form: state => state.form, //MENGAMBIL STATE DATA
            isShow: state => state.isShow,
        }),
    },
    methods: {
        ...mapMutations(['CLEAR_ERRORS']),
        ...mapMutations('businessTripApplication', ['CLEAR_FORM']),
        ...mapActions('businessTripApplication', ['show']),
        preview(path){
            this.imgUrl = path
            this.$bvModal.show("modal-preview-image-target");
        },
        hideModal() {
            this.imgUrl = ""
            this.$bvModal.hide("modal-preview-image-target");
        },
        handleAttachment(item, event){
            this.imgBase64(event.target.files[0], item)
        },
        imgBase64(file, item){
            const reader = new FileReader();
            reader.onload = (event) => {
                item.file = event.target.result ?? null
            }
            reader.readAsDataURL(file)
        },
    },
    //KETIKA PAGE INI DITINGGALKAN MAKA
    destroyed() {
        //FORM DI BERSIHKAN
        this.CLEAR_ERRORS();
        this.CLEAR_FORM();
    }
};
</script>
