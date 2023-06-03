function authError(error) {
    let errorMessages = [];
    let detailedMessages = [];

    let errorMessage =
        error.response.data.message || "The given data was invalid.";
    errorMessages.push(errorMessage);

    if (error.response.data.errors) {
        detailedMessages = [].concat.apply(
            [],
            Object.values(error.response.data.errors)
        );
        errorMessages = errorMessages.concat(detailedMessages);
    }

    errorMessages.forEach(message => {
        let toast = Vue.toasted.show(message, {
            theme: "toasted-primary",
            position: "top-right",
            duration: 5000
        });
    });
}

function success(module, type){
    Vue.toasted.success(`${module} Berhasil di${type}`, {
        position: "top-right",
        duration: 5000,
    });
}

function error(module, type){
    Vue.toasted.error(`${module} Gagal di${type}`, {
        position: "top-right",
        duration: 5000,
    });
}

export {
    authError,success, error
}
