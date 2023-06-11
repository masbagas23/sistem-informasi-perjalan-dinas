import layout from "@app/views/admin/layout/index.vue"
import mstUser from "./users/router"
import mstRole from "./roles/router"
import mstJobPosition from "./job-positions/router"
import mstJobCategory from "./job-categories/router"
import mstCostCategory from "./cost-categories/router"
import mstVehicle from "./vehicles/router"
import mstCustomer from "./customers/router"
import businessTripApplication from "./business-trip-applications/router"
import vehicleLoan from "./vehicle-loans/router"

const dashboard = {
    path: '',
    component: { render (c) { return c('router-view') }},
    meta: { requiresAuth: true},
    children: [
        {
            path: '',
            name: 'dashboard.index',
            component: () => import(/* webpackChunkName: "admin" */ './dashboard.vue'),
            meta: { title: 'Dashborad', disableBreadcrumb:false }
        },
    ]
}
export default {
    path: "/admin",
    component: layout,
    children: [
        dashboard,
        ...mstUser,
        ...mstRole,
        ...mstJobPosition,
        ...mstJobCategory,
        ...mstCostCategory,
        ...mstVehicle,
        ...mstCustomer,
        ...businessTripApplication,
        ...vehicleLoan,
    ]
}