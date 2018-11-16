import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
import Login from './Login.vue';


Vue.use(Vuetify);

new Vue({
    el: '#app',
    render: h => h(Login),
});
