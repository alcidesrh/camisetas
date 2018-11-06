import TallaList from '../components/talla/List.vue';
import TallaCreate from '../components/talla/Create.vue';
import TallaUpdate from '../components/talla/Update.vue';
import TallaShow from '../components/talla/Show.vue';

export default [
  { name: 'TallaList', path: '/tallas/', component: TallaList },
  { name: 'TallaCreate', path: '/tallas/create', component: TallaCreate },
  { name: 'TallaUpdate', path: "/tallas/edit/:id", component: TallaUpdate },
  { name: 'TallaShow', path: "/tallas/show/:id", component: TallaShow  }
];
