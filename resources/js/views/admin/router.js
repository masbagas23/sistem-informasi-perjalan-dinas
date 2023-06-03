import layout from "@app/views/admin/layout/index.vue"
import mstUser from "./users/router"
import mstRole from "./roles/router"

const dashboard = {
    path: '',
    component: { render (c) { return c('router-view') }},
    meta: { requiresAuth: true},
    children: [
        {
            path: '',
            name: 'dashboard.index',
            component: () => import(/* webpackChunkName: "admins" */ './dashboard.vue'),
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
    ]
}