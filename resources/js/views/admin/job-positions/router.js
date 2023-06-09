export default [
    {
        path: 'job-positions',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'jobPositions.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Jabatan', module:'Data Master'}
            },
        ]
    }
]
