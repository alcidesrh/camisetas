import UserList from '../components/user/List.vue';
import ListPedido from '../components/user/ListPedido.vue';

export default [
    { name: 'UserList', path: '/usuarios/', component: UserList },
    { name: 'ListPedido', path: '/pedidos-user/', component: ListPedido }
];
