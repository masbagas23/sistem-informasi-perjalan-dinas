import moment from "moment"

function formatDate(date) {
    moment.lang("id")
    return moment(date).format('DD MMM YYYY')
}

export { formatDate }