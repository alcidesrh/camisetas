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

import user from './store/modules/user/';
import userRoutes from './routes/user';

import stock from './store/modules/stock/';
import stockRoutes from './routes/stock';

import venta from './store/modules/venta/';
import ventaRoutes from './routes/venta';

Vue.use(Vuex);
Vue.use(VueRouter);

import es from './i18n-es/es'

Vue.use(Vuetify, {
    lang: {
        locales: {'Es-es': es},
        current: 'Es-es'
    }
});

const store = new Vuex.Store({
    modules: {
        producto,
        talla,
        user,
        stock,
        venta
    }
});

const router = new VueRouter({
    mode: 'history',
    routes: [
        ...productoRoutes,
        ...tallaRoutes,
        ...userRoutes,
        ...stockRoutes,
        ...ventaRoutes
    ]
});

new Vue({
    el: '#app',
    store,
    router,
    render: h => h(App),
});
