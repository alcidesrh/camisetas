import PedidoList from '../components/pedido/List.vue';
import PedidoCreate from '../components/pedido/Create.vue';
import PedidoUpdate from '../components/pedido/Update.vue';

export default [
  { name: 'PedidoList', path: '/pedidos/', component: PedidoList },
  { name: 'PedidoCreate', path: '/pedidos/create', component: PedidoCreate },
  { name: 'PedidoUpdate', path: "/pedidos/edit/:id", component: PedidoUpdate }
];
