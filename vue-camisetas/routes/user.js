import UserList from '../components/user/List.vue';
import UserCreate from '../components/user/Create.vue';
import UserUpdate from '../components/user/Update.vue';
import UserShow from '../components/user/Show.vue';

export default [
  { name: 'UserList', path: '/usuarios/', component: UserList },
  { name: 'UserCreate', path: '/users/create', component: UserCreate },
  { name: 'UserUpdate', path: "/users/edit/:id", component: UserUpdate },
  { name: 'UserShow', path: "/users/show/:id", component: UserShow  }
];
