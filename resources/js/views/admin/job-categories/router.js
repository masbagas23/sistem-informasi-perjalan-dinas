export default [
    {
        path: 'job-categories',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'jobCategories.index',
                component: () => import(/* webpackChunkName: "admin" */ './index.vue'),
                meta: { title: 'Kategori Pekerjaan', module:'Data Master'}
            },
        ]
    }
]
