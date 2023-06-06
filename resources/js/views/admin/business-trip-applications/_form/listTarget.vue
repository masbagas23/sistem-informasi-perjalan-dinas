<template>
    <div>
        <!-- sticky-header -->
        <div class="float-right mb-2">
            <button @click="addTarget" class="btn btn-sm btn-success"><b-icon icon="plus"></b-icon> Tambah</button>
        </div>
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

            <template v-slot:cell(name)="row">
                <input placeholder="Nama" type="text" class="form-control" v-model="row.item.name">
            </template>

            <template v-slot:cell(description)="row">
                <!-- <input placeholder="Deskripsi Tugas" type="text" class="form-control" v-model="row.item.name"> -->
                <b-form-textarea v-model="row.item.description" placeholder="Deskripsi Tugas" rows="1" max-rows="2" ></b-form-textarea>
            </template>
        </b-table>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
    props: ["form"],
    created() {
        this.loadUser({ filter_job_position: "participant" });
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
                    key: "description",
                    label: "Deskripsi"
                },
                {
                    key: "action",
                    label: "Aksi",
                    thStyle: "width:80px;text-align:center"
                }
            ]
        };
    },
    computed: {
        ...mapState(["errors"]), //MENGAMBIL STATE ERRORS
    },
    methods: {
        ...mapActions("mstUser", { loadUser: "loadList" }),
        addTarget(){
            this.form.targets.push({id:"", name:"", description:"", duration:"", start_date:"", end_date:"", status:"", reason:""})
        }
    }
};
</script>
