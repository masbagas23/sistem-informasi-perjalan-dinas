export default [
    {
        path: 'cost-categories',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'costCategories.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Kategori Biaya', module:'Data Master'}
            },
        ]
    }
]
