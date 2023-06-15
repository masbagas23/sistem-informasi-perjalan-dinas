export default [
    {
        path: 'expenses',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'expenses.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Biaya Pengeluaran', module:'Transaksi'}
            },
        ]
    }
]
