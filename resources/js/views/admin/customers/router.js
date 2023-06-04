export default [
    {
        path: 'customers',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'customers.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Pelanggan', module:'Data Master'}
            },
        ]
    }
]
