
import Vue from 'vue'
import Vuex from 'vuex';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
import App from './App.vue';

import producto from './store/modules/producto/';
import productoRoutes from './routes/producto';

import talla from './store/modules/talla/';
import tallaRoutes from './routes/talla';

Vue.use(Vuex);
Vue.use(VueRouter);

import es from './i18n-es/es'
Vue.use(Vuetify, {
    lang: {
        locales: { 'Es-es': es, },
        current: 'Es-es'
    }
});

const store = new Vuex.Store({
    modules: {
        producto,
        talla
    }
});

const router = new VueRouter({
    mode: 'history',
    routes: [
        ...productoRoutes,
        ...tallaRoutes
    ]
});

new Vue({
    el: '#app',
    store,
    router,
    render: h => h(App),
});
