/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("@app/utils/bootstrap");

import router from "./router";
import "@app/utils/axios";
import store from "@app/utils/vuex";
import Toasted from "vue-toasted";
import _ from 'lodash';
import bootstrapVue from "@app/utils/bootstrap-vue"
import Chart from "chart.js";
import Vue from "vue";
import vSelect from "@app/utils/vue-select";
import VueSimpleAlert from "vue-simple-alert"
import Element from 'element-ui'
import locale from 'element-ui/lib/locale/lang/en'
import VueApexCharts from 'vue-apexcharts'



window.Vue = require("vue").default;
Vue.use(Toasted,{iconPack : 'material'});
Vue.use(VueSimpleAlert)
Vue.use(Element, {locale})
Vue.use(VueApexCharts)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component("app", require("./App.vue").default);
Vue.component('apexchart', VueApexCharts)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    router,
    store,
    bootstrapVue,
    vSelect,
    el: "#app"
});
