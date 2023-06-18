export default [
    {
        path: 'report-expenses',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'reportExpenses.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Rekap Biaya Pengeluaran', module:'Laporan'}
            },
        ]
    }
]
