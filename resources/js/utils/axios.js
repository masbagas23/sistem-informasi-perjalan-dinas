import axios from "axios";

axios.defaults.baseURL = "/api/"; // change this if you want to use a different url for APIs
axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token');

axios.interceptors.response.use(
    response => {
        return response
    },
    error => {
        // const res = get(error, 'response')
        // console.log(res.status)

        // switch (res.status) {
        //     case 400:
        //         error.message = `Data Tidak Sesuai...`
        //         break
        //     case 401:
        //         error.message = `User Tidak Diketahui, Mohon Login Kembali...`
        //         break
        //     case 403:
        //         error.message = `Maaf, Anda Tidak Memiliki Akses...`
        //         break
        //     case 404:
        //         error.message = `Maaf, Halaman Tidak Ditemukan...`
        //         break
        //     case 422:
        //         error.message = `Data Tidak Sesuai...`
        //         break
        //     case 500:
        //         error.message = `Server Bermasalah, Coba Beberapa Saat Lagi...`
        //         break
        //     case 503:
        //         error.message = `Server Sedang Sibuk, Coba Beberapa Saat Lagi...`
        //         break
        //     case 504:
        //         error.message = `Server Sedang Sibuk, Coba Beberapa Saat Lagi...`
        //         break
        //     case 502:
        //         error.message = `Server Sedang Sibuk, Coba Beberapa Saat Lagi...`
        //         break
        //     default:
        //         error.message = 'Koneksi Anda Terputus...'
        //         break
        // }
        handleError(error)
        return Promise.reject(error)
    }
);

function handleError(error) {
    console.log(error.message)
    Vue.toasted.error(error.message,{
        duration : 3000,
        action : {
            text : 'Close',
            onClick : (e, toastObject) => {
                toastObject.goAway(0);
            }
        },
    });
}

export default axios;
