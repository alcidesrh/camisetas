import VentaList from '../components/venta/List.vue';
import VentaCreate from '../components/venta/Create.vue';
import VentaUpdate from '../components/venta/Update.vue';
import VentaShow from '../components/venta/Show.vue';

export default [
  { name: 'VentaList', path: '/ventas', component: VentaList },
  { name: 'VentaCreate', path: '/ventas/create', component: VentaCreate },
  { name: 'VentaUpdate', path: "/ventas/edit/:id", component: VentaUpdate },
  { name: 'VentaShow', path: "/ventas/show/:id", component: VentaShow  }
];
