import Vue from "vue";

import Router from "vue-router";
import store from "@app/utils/vuex";
import routers from "@app/views/admin/router"


Vue.use(Router);

let router = new Router({
    mode: "history",
    routes: [
        {
            path: "/",
            name: "home",
            component: () => import("./views/home/index.vue")
        },
        {
            path: "/login/:user_id?",
            name: "login",
            component: () => import("./views/login/index.vue")
        },
        {
            path: "/register",
            name: "register",
            component: () => import("./views/register/index.vue")
        },
        {
            path: "/verify/user/:id",
            name: "verify",
            props: true,
            component: () => import("./views/verify/index.vue")
        },
        {
            path: "/forgot-password",
            name: "forgot",
            component: () => import("./views/forgot/index.vue")
        },
        {
            path: "/reset/:token",
            name: "reset",
            component: () => import("./views/reset/index.vue")
        },
        /**
         * Admin routes
         */
        routers
    ]
});

router.beforeEach(async (to, from, next) => {
    if(to.name === 'login' && store.getters.user)next('/app');
    else if (to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters.user) next();
        else next("/login");
    } else {
        next();
    }
});

export default router;
