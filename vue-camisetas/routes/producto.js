import ProductoList from '../components/producto/List.vue';
import ProductoCreate from '../components/producto/Create.vue';
import ProductoUpdate from '../components/producto/Update.vue';
import ProductoShow from '../components/producto/Show.vue';

export default [
  { name: 'ProductoList', path: '/productos/', component: ProductoList },
  { name: 'ProductoCreate', path: '/productos/create', component: ProductoCreate },
  { name: 'ProductoUpdate', path: "/productos/edit/:id", component: ProductoUpdate },
  { name: 'ProductoShow', path: "/productos/show/:id", component: ProductoShow  }
];
