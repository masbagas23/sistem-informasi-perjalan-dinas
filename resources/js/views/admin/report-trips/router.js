export default [
    {
        path: 'report-trips',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'reportTrips.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Laporan Perjalan Dinas', module:'Laporan'}
            },
        ]
    }
]
