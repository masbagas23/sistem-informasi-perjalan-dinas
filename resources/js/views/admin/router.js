import project from "./projects/router"
import layout from "@app/views/admin/layout/index.vue"
const dashboard = {
    path: '',
    component: { render (c) { return c('router-view') }},
    meta: { requiresAuth: true},
    children: [
        {
            path: '',
            name: 'dashboard.index',
            component: () => import(/* webpackChunkName: "projects" */ './dashboard.vue'),
            meta: { title: 'Dashborad', disableBreadcrumb:false }
        },
    ]
}
export default {
    path: "/admin",
    component: layout,
    children: [
        dashboard,
        ...project
    ]
}