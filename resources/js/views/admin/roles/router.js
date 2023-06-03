export default [
    {
        path: 'roles',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'roles.index',
                component: () => import(/* webpackChunkName: "projects" */ './index.vue'),
                meta: { title: 'Hak Akses', module:'Manajemen Pengguna'}
            },
        ]
    }
]
