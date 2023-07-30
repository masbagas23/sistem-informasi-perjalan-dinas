<template>
    <div>
        <!-- sticky-header -->
        <div class="row d-flex align-items-end">
            <div class="col-8">
                <small><p class="text-danger" v-if="errors.users">Minimal tambahkan 1 peserta</p></small>
                <small><i>* Peserta pertama merupakan koordinator</i></small>
            </div>
            <div class="col-4 text-right">
                <button @click="addUser" class="btn btn-sm btn-success"><b-icon icon="plus"></b-icon> Tambah</button>
            </div>
        </div>
        <b-table
            :fields="fields"
            show-empty
            bordered
            small
            breceptioned
            responsive="sm"
            class="py-2"
            :items="form.users"
        >
            <template v-slot:cell(index)="row">
                {{ row.index + 1 }}
            </template>

            <template v-slot:cell(action)="row">
                <div class="text-center">
                    <b-badge
                        v-show="row.index > 0"
                        title="Hapus"
                        class="btn"
                        @click="removeUser(row.index)"
                        pill
                        variant="danger"
                        ><b-icon icon="trash"></b-icon
                    ></b-badge>
                </div>
            </template>

            <template v-slot:cell(name)="row">
                <v-select
                    class="boot-style"
                    v-model="row.item.user_id"
                    label="first_name"
                    :reduce="item => item.id"
                    :selectable="selectable"
                    :options="users.data"
                    placeholder="Pilih Pegawai"
                >
                    <template #option="{ first_name, last_name, nip, job_position}">
                        <p class="m-0">{{ first_name }} {{last_name}}</p>
                        <small><em>{{ nip }} - {{job_position.name}}</em></small>
                    </template>
                    <template #selected-option="{ first_name, last_name }">
                        <span>{{first_name}} {{last_name}}</span>
                        <b-badge class="ml-2" v-show="row.item.is_leader" pill variant="primary">Koordinator</b-badge>
                    </template>
                </v-select>
            </template>
        </b-table>
    </div>
</template>
<script>
import { mapState, mapMutations, mapActions } from "vuex";

export default {
    props: ["form"],
    created() {
        this.loadUser({ filter_job_position: "participant", start_date:this.form.start_date, end_date:this.form.end_date });
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
                    label: "Pegawai"
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
        ...mapState("mstUser", {
            users: state => state.collectionList, //MENGAMBIL STATE DATA
            isShow: state => state.isShow
        }),
    },
    methods: {
        ...mapActions("mstUser", { loadUser: "loadList" }),
        addUser(){
            this.form.users.push({id:'',name:'',is_leader: false})
        },
        removeUser(index){
            this.form.users.splice(index,1)
        },
        selectable(item) {
            const users = this.form.users.map(user => user.user_id)
            return !_.includes(users, item.id)
        }
    }
};
</script>
