// import AdminLayout from "@app/views/admin/layout/index";

export default [
    {
        path: 'users',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'users.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'User', module:'Manajemen Pengguna'}
            },
        ]
    }
]
