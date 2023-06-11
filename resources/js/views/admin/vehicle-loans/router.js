export default [
    {
        path: 'vehicle-loans',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'vehicleLoans.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Peminjaman Kendaraan', module:'Transaksi'}
            },
        ]
    }
]
