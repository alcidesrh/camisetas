import StockList from '../components/stock/List.vue';
import StockCreate from '../components/stock/Create.vue';
import StockUpdate from '../components/stock/Update.vue';


export default [
    {name: 'StockList', path: '/stocks/', component: StockList},
    {name: 'StockCreate', path: '/stocks/create', component: StockCreate},
    {name: 'StockUpdate', path: "/stocks/edit/:id", component: StockUpdate},
];
