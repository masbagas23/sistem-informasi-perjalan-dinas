export default [
    {
        path: 'vehicles',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'vehicles.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Kendaraan', module:'Data Master'}
            },
        ]
    }
]
