import moment from "moment"

function formatDate(date) {
    moment.lang("id")
    return moment(date).format('DD MMM YYYY')
}

function formatCurrency(bilangan) {
    var reverse = bilangan.toString().split('').reverse().join(''),
        ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');

    return ribuan
}

export { formatDate, formatCurrency }