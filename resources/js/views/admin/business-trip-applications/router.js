export default [
    {
        path: 'business-trips',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'businessTrips.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Perjalan Dinas', module:'Transaksi'}
            },
        ]
    }
]
