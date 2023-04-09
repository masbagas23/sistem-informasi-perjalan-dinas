import $axios from "@app/utils/axios"

function defaultForm() {
    return {
        name: "",
        address: "",
        pic_name: "",
        pic_phone_number: "",
        province_id: "",
        city_id: "",
        district_id: "",
        description: "",
        value: "",
        is_active : true,
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
            name: payload.name,
            address: payload.address,
            pic_name: payload.pic_name,
            pic_phone_number: payload.pic_phone_number,
            province_id: payload.province_id,
            city_id: payload.city_id,
            district_id: payload.district_id,
            description: payload.description,
            value: payload.value,
            is_active : payload.is_active,
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
                .get(`/projects`, { params: payload })
                .then(response => {
                    //SIMPAN DATA KE STATE MELALUI MUTATIONS
                    commit("ASSIGN_DATA", response.data);
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
        return new Promise((resolve, reject) => {
            $axios.get(`/project-list`).then(response => {
                //SIMPAN DATA KE STATE MELALUI MUTATIONS
                commit("ASSIGN_LIST", response.data);
                resolve(response.data);
            });
        });
    },

    //UNTUK MENGAMBIL SINGLE DATA DARI SERVER BERDASARKAN ID
    filterTabList({ commit }, payload) {
        commit('SET_BUSY', true)
        return new Promise((resolve, reject) => {
            //MELAKUKAN REQUEST DENGAN MENGIRIMKAN ID DI URL
            $axios.get(`/project-tab-alert`, { params: payload })
            .then((response) => {
                //SIMPAN DATA KE STATE MELALUI MUTATIONS
                commit('ASSIGN_FILTER_DATA', response.data.data)
                resolve(response.data)
                commit('SET_BUSY', false)
            }).catch(error => {
                commit('SET_BUSY', false)
                return []
            })
        })
    },
    //FUNGSI UNTUK MENAMBAHKAN DATA BARU
    store({ dispatch, commit, state }) {
        return new Promise((resolve, reject) => {
            //MENGIRIMKAN PERMINTAAN KE SERVER DAN MELAMPIRKAN DATA YANG AKAN DISIMPAN
            //DARI STATE FORM
            $axios
                .post(`/projects`, state.form)
                .then(response => {
                    //APABILA BERHASIL KITA MELAKUKAN REQUEST LAGI
                    //UNTUK MENGAMBIL DATA TERBARU
                    dispatch("load", state.tableParams).then(() => {
                        resolve(response.data);
                    });
                    dispatch("filterTabList", state.tableParams).then(() => {
                        resolve(response.data);
                    });
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
        commit("SET_BUSY", true);
        return new Promise((resolve, reject) => {
            //MELAKUKAN REQUEST DENGAN MENGIRIMKAN ID DI URL
            $axios.get(`/projects/${payload}`).then(response => {
                //APABIL BERHASIL, DI ASSIGN KE FORM
                commit("ASSIGN_FORM", response.data.data);
                resolve(response.data);
                commit("SET_BUSY", false);
            });
        });
    },

    download({ commit },payload) {
        let { file } = payload
        commit("SET_DOWNLOAD", true);
        return new Promise((resolve, reject) => {
            //MELAKUKAN REQUEST DENGAN MENGIRIMKAN ID DI URL
            $axios
                .get(`/project-export-${file}`, {
                   responseType: "blob",
                    params: payload
                })
                .then(response => {
                    var fileType = null
                    if (file=="salary-slip") {
                        fileType = "application/pdf"
                    }else if (file=="permata") {
                        // fileType = "application/vnd.ms-excel"
                        fileType = "text/csv"
                    }else if (file=="talenta") {
                        fileType = "application/vnd.ms-excel"
                    }else{
                        fileType = file == "pdf" ? "application/pdf" : "application/vnd.ms-excel"
                    }

                    const blob = new Blob([response.data], { type: fileType });
                    const link = document.createElement("a");
                    link.href = URL.createObjectURL(blob);
                    link.download =
                        "projects-" + new Date().toISOString().substring(0, 10);
                    link.click();
                    URL.revokeObjectURL(link.href);
                    commit("SET_DOWNLOAD", false);
                })
                .catch(error => {
                    commit("SET_DOWNLOAD", false);
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
                .put(`/projects/${payload}`, state.form)
                .then(response => {
                    //FORM DIBERSIHKAN
                    // commit("CLEAR_FORM");
                    resolve(response.data);
                    dispatch("filterTabList", state.tableParams).then(() => {
                        resolve(response.data);
                    });
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
    //UNTUK MENGHITUNG PAYROLL PROCESS
    calculate({ dispatch, commit, state }) {
        return new Promise((resolve, reject) => {
            //MENGIRIMKAN PERMINTAAN KE SERVER DAN MELAMPIRKAN DATA YANG AKAN DISIMPAN
            //DARI STATE FORM
            $axios
                .post(`/payroll-calculation`, state.form)
                .then(response => {
                    //APABILA BERHASIL KITA MELAKUKAN REQUEST LAGI
                    //UNTUK MENGAMBIL DATA TERBARU
                    dispatch("load", state.tableParams).then(() => {
                        resolve(response.data);
                    });
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
    destroy({ dispatch, state }, payload) {
        return new Promise((resolve, reject) => {
            //MENGIRIM PERMINTAAN KE SERVER UNTUK MENGHAPUS DATA
            $axios
                .delete(`/projects/${payload}`)
                .then(response => {
                    //APABILA BERHASIL, FETCH DATA TERBARU DARI SERVER
                    dispatch("load", state.tableParams).then(() => resolve());
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
