import AdminLayout from "@app/views/admin/layout/index";

export default [
    {
        path: 'projects',
        component: { render (c) { return c('router-view') }},
        meta: { requiresAuth: true},
        children: [
            {
                path: '',
                name: 'projects.index',
                component: () => import(/* webpackChunkName: "projects" */ './index.vue'),
                meta: { title: 'Projek', module:'Master'}
            },
        ]
    }
]
