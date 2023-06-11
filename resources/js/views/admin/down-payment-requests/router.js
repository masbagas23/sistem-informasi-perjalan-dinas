export default [
    {
        path: 'down-payment-requests',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'downPaymentRequests.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Uang Muka', module:'Transaksi'}
            },
        ]
    }
]
