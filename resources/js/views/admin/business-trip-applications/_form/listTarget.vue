<template>
    <div>
        <!-- sticky-header -->
        <small><p class="text-danger" v-if="errors.users">Minimal tambahkan 1 tugas</p></small>
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

            <template v-slot:cell(action)>
                <b-badge
                    title="Hapus"
                    class="btn"
                    @click="removeTarget(index)"
                    pill
                    variant="danger"
                    ><b-icon icon="trash"></b-icon
                ></b-badge>
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
        addTarget(){
            this.form.targets.push({id:"", name:"", description:"", duration:"", start_date:"", end_date:"", status:"", reason:""})
        },
        removeTarget(index){
            this.form.targets.splice(index,1)
        }
    }
};
</script>
