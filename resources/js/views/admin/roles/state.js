import $axios from "@app/utils/axios"

function defaultForm() {
    return {
        code: "",
        name: "",
        description:"",
        created_at: "",
        updated_at: "",
        deleted_at: ""
    };
}

const state = () => ({
    collection: [], //UNTUK MENAMPUNG DATA COLLECTION YANG DIDAPATKAN DARI DATABASE
    collectionList: [],

    //UNTUK MENAMPUNG VALUE DARI FORM INPUTAN NANTINYA
    form: defaultForm(),
    isBusy: false,
    isShow: false,
    isLoading: false,
    tableParams: {
        page: 1, //UNTUK MENCATAT PAGE PAGINATE YANG SEDANG DIAKSES
        per_page: 10,
        order_column: "updated_at",
        order_direction: true, //ASCEDING
        pageOptions: [
            { text: 10, value: 10 },
            { text: 20, value: 20 },
            { text: 50, value: 50 },
            { text: 100, value: 100 }
        ]
    }
});

const mutations = {
    //MEMASUKKAN DATA KE STATE COLLECTION
    ASSIGN_DATA(state, payload) {
        state.collection = payload;
    },
    //MEMASUKKAN DATA KE STATE COLLECTION
    ASSIGN_LIST(state, payload) {
        state.collectionList = payload;
    },
    //MEMASUKKAN DATA KE STATE COLLECTION
    ASSIGN_FILTER_DATA(state, payload) {
        state.filterTabs = payload
    },
    //MENGUBAH DATA STATE PAGE
    SET_BUSY(state, payload) {
        state.isBusy = payload;
    },
    SET_SHOW(state, payload) {
        state.isShow = payload;
    },
    SET_LOADING(state, payload) {
        state.isLoading = payload;
    },
    //MENGUBAH DATA STATE PAGE
    SET_DOWNLOAD(state, payload) {
        state.isDownload = payload;
    },
    //MENGUBAH DATA STATE PAGE
    SET_PAGE(state, payload) {
        state.tableParams.page = payload;
    },
    //MENGUBAH DATA STATE PAGE
    SET_TABLE_PARAMS(state, payload) {
        state.tableParams = payload;
    },
    //MENGUBAH DATA STATE FORM
    ASSIGN_FORM(state, payload) {
        state.form = {
            code: payload.code,
            name: payload.name,
            description: payload.description,
            updated_at: payload.updated_at,
            deleted_at: payload.deleted_at,
        };
    },
    //ME-RESET STATE FORM MENJADI KOSONG
    CLEAR_FORM(state) {
        state.form = defaultForm();
    }
};

const actions = {
    //FUNGSI INI UNTUK MELAKUKAN REQUEST DATA DARI SERVER
    load({ commit }, payload) {
        commit("SET_BUSY", true);
        return new Promise((resolve, reject) => {
            $axios
                .get(`/roles`, { params: payload })
                .then(response => {
                    //SIMPAN DATA KE STATE MELALUI MUTATIONS
                    commit("ASSIGN_DATA", response.data);
                    commit("CLEAR_FORM");
                    resolve(response.data);
                    commit("SET_BUSY", false);
                })
                .catch(error => {
                    commit("SET_BUSY", false);
                    return [];
                });
        });
    },
    //FUNGSI INI UNTUK MELAKUKAN REQUEST DATA DARI SERVER
    loadList({ commit }, payload) {
        commit("SET_LOADING", false);
        return new Promise((resolve, reject) => {
            $axios.get(`/role-list`).then(response => {
                //SIMPAN DATA KE STATE MELALUI MUTATIONS
                commit("ASSIGN_LIST", response.data);
                resolve(response.data);
                commit("SET_LOADING", false);
            });
        });
    },
    //FUNGSI UNTUK MENAMBAHKAN DATA BARU
    store({ commit, state }) {
        return new Promise((resolve, reject) => {
            //MENGIRIMKAN PERMINTAAN KE SERVER DAN MELAMPIRKAN DATA YANG AKAN DISIMPAN
            //DARI STATE FORM
            $axios
                .post(`/roles`, state.form)
                .then(response => {
                    //APABILA BERHASIL KITA MELAKUKAN REQUEST LAGI
                    //UNTUK MENGAMBIL DATA TERBARU
                    resolve(response.data);
                })
                .catch(error => {
                    //APABILA TERJADI ERROR VALIDASI
                    //DIMANA LARAVEL MENGGUNAKAN CODE 422
                    if (error.response.status == 422) {
                        //MAKA LIST ERROR AKAN DIASSIGN KE STATE ERRORS
                        commit("SET_ERRORS", error.response.data.errors, {
                            root: true
                        });
                    }
                    reject(error.response);
                });
        });
    },
    //UNTUK MENGAMBIL SINGLE DATA DARI SERVER BERDASARKAN ID
    show({ commit }, payload) {
        commit("SET_SHOW", true);
        return new Promise((resolve, reject) => {
            //MELAKUKAN REQUEST DENGAN MENGIRIMKAN ID DI URL
            $axios.get(`/roles/${payload}`).then(response => {
                //APABIL BERHASIL, DI ASSIGN KE FORM
                commit("ASSIGN_FORM", response.data.data);
                resolve(response.data);
                commit("SET_SHOW", false);
            });
        });
    },
    //UNTUK MENGUPDATE DATA BERDASARKAN ID YANG SEDANG DIEDIT
    update({ state, commit }, payload) {
        return new Promise((resolve, reject) => {
            //MELAKUKAN REQUEST DENGAN MENGIRIMKAN ID DI URL
            //DAN MENGIRIMKAN DATA TERBARU YANG TELAH DIEDIT
            //MELALUI STATE FORM
            $axios
                .put(`/roles/${payload}`, state.form)
                .then(response => {
                    //FORM DIBERSIHKAN
                    // commit("CLEAR_FORM");
                    resolve(response.data);
                })
                .catch(error => {
                    //APABILA TERJADI ERROR VALIDASI
                    //DIMANA LARAVEL MENGGUNAKAN CODE 422
                    if (error.response.status == 422) {
                        //MAKA LIST ERROR AKAN DIASSIGN KE STATE ERRORS
                        commit("SET_ERRORS", error.response.data.errors, {
                            root: true
                        });
                    }
                    reject(error.response);
                });
        });
    },
    //MENGHAPUS DATA
    destroy({}, payload) {
        return new Promise((resolve, reject) => {
            //MENGIRIM PERMINTAAN KE SERVER UNTUK MENGHAPUS DATA
            $axios
                .delete(`/roles/${payload}`)
                .then((response) => {
                    //APABILA BERHASIL, FETCH DATA TERBARU DARI SERVER
                    resolve(response);
                });
        });
    }
};

export default {
    namespaced: true,
    state,
    actions,
    mutations
};
