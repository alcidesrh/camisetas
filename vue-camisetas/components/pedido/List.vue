<template>
    <div class="pa-5">
        <v-snackbar
                :color="snackbarColor"
                :timeout="3000"
                :top="true"
                :multi-line="true"
                v-model="snackbar"
        >
            {{ snackbarText }}
        </v-snackbar>
        <v-card>
            <v-container style="position: fixed; z-index: 2001" fill-height justify-center
                         v-show="loading || deleteLoading || userLoading">
                <v-progress-circular indeterminate :size="70" :width="3" color="success"></v-progress-circular>
            </v-container>
            <v-tooltip top>
                <v-btn color="primary"
                       slot="activator"
                       dark
                       fab
                       fixed
                       bottom
                       right
                       @click="$router.push({name: 'PedidoCreate'})"
                >
                    <v-icon>add</v-icon>
                </v-btn>
                <span>Añadir Pedido</span>
            </v-tooltip>
            <v-alert type="info" :value="true" v-show="items.length == 0" class="mt-2" style="width: 100%">
                No hay elementos para mostrar
            </v-alert>
            <div v-show="items.length != 0">
                <v-card-title>

                    <v-flex headline v-if="user">
                        Pedidos de {{user.fullName}}
                    </v-flex>
                    <v-flex v-else headline>
                        Pedidos
                    </v-flex>
                    <v-flex style="max-width: 350px; text-align: right">
                        <v-select
                                v-model="selectUser"
                                :items="users"
                                label="Filtrar por usuario"
                                prepend-inner-icon="person"
                                item-text="fullName"
                                item-value="id"
                                @change="filterByUser"
                        ></v-select>

                    </v-flex>
                    <v-flex style="max-width: 30px;">
                        <v-btn icon class="mx-0 d-inline-block"  @click="resetList" slot="activator">
                            <v-icon>refresh</v-icon>
                        </v-btn>
                    </v-flex>
                </v-card-title>
                <v-data-table
                        :headers="headers"
                        :items="items"
                        :search="search"
                        :rows-per-page-items="[10,5,25,{'text':'All','value':-1}]"
                        no-data-text=""
                        :disable-initial-sort="true"
                >
                    <template slot="items" slot-scope="props">
                        <td>{{ formatDate(props.item.createAt) }}</td>
                        <td>{{ formatDate(props.item.lastUpdate) }}</td>
                        <td>{{ props.item.user.fullName }}</td>
                        <td>
                            <v-chip v-for="producto in props.item.productos" :key="producto.id">
                                <img height="35" v-bind:src="getImageUrl(producto.producto.imagen.path)"
                                     class="py-1"/>
                                <label class="pl-2 d-inline">{{ producto.producto.nombre }}</label>
                            </v-chip>
                        </td>
                        <td>{{ props.item.stock }}</td>
                        <td>{{ props.item.venta }}</td>
                        <td class="justify-center layout px-0">
                            <v-tooltip top>
                                <v-btn icon class="mx-0"  @click="ventaUrl(props.item)" slot="activator">
                                    <v-icon color="orange">shopping_basket</v-icon>
                                </v-btn>
                                <span>Ver Venta</span>
                            </v-tooltip>
                            <v-btn icon class="mx-0" @click="editUrl(props.item)">
                                <v-icon color="teal">edit</v-icon>
                            </v-btn>
                            <v-btn icon class="mx-0" @click="deleteItem(props.item)">
                                <v-icon color="red">delete</v-icon>
                            </v-btn>
                        </td>
                    </template>
                    <v-alert slot="no-results" :value="true" color="error" icon="warning">
                        No se encontraron resultado para "{{ search }}".
                    </v-alert>
                </v-data-table>
            </div>
        </v-card>
    </div>
</template>
<script>
    import {mapGetters} from 'vuex';
    import {API_HOST} from '../../config/_entrypoint';
    import moment from 'moment';

    export default {
        data() {
            return {
                selectUser: false,
                pedido: {user: false, productos: [], stock: []},
                valid: true,
                search: '',
                headers: [
                    {text: 'Creado', value: 'createAt'},
                    {text: 'Última actualización', value: 'lastUpdate'},
                    {text: 'Usuario', value: 'user.fullName'},
                    {text: 'Productos', value: 'producto.nombre'},
                    {text: 'Stock', value: 'stock'},
                    {text: 'Vendido', value: 'venta'},
                    {text: '', value: ''}
                ],
                dialog: false,
                editedIndex: -1,
                snackbar: false,
                snackbarText: '',
                snackbarColor: 'success',
                flag: false,
            }
        },
        computed: {
            ...mapGetters({
                deletedItem: 'pedido/del/deleted',
                errorList: 'pedido/list/error',
                errorDelete: 'pedido/del/error',
                items: 'pedido/list/items',
                loading: 'pedido/list/loading',
                deleteLoading: 'pedido/del/loading',
                user: 'user/update/retrieved',
                productos: 'producto/list/items',
                tallas: 'talla/list/items',
                userLoading: 'user/update/retrieveLoading',
                users: 'user/list/items',
            })
        },
        watch: {
            errorList(message) {
                this.error(message);
            },
            errorDelete(message) {
                this.error(message);
            },
            snackbar(val) {
                val || (this.snackbarColor = 'success')
            }
        },
        methods: {
            resetList(){
                this.selectUser = false;
                this.$store.dispatch('user/update/reset');
                this.getItems();

            },
            filterByUser(){
                this.$store.dispatch('user/update/retrieve', '/users/' + this.selectUser).then(() =>{
                    this.getItems();
                })
            },
            ventaUrl(item){
                return this.user?this.$router.push({name: 'PedidoVenta', params: {id: item['id'], user: this.user.id} }):this.$router.push({name: 'PedidoVenta', params: {id: item['id']} })
            },
            editUrl(item){
                return this.user?this.$router.push({name: 'PedidoUpdate', params: {id: item['id'], user: this.user.id} }):this.$router.push({name: 'PedidoUpdate', params: {id: item['id']} })
            },
            formatDate(date){
                return date?moment(date).format('DD/MM/YYYY'):"";
            },
            getImageUrl(path) {
                return API_HOST + '/' + path;
            },
            error(message) {
                this.flag = true;
                this.snackbarColor = 'error';
                this.snackbarText = message;
                this.snackbar = true;
            },
            deleteItem(item) {
                if (confirm('Seguro quieres eliminar este elemento?')) {

                    this.$store.dispatch('pedido/del/delete', item).then(
                        () => {
                            if (this.flag) {
                                this.flag = false;
                                return;
                            }
                            this.snackbarText = 'Se ha eliminado';
                            this.snackbar = true;
                            this.getItems();
                        })
                }
            },
            close() {
                this.dialog = false;
                this.editedIndex = -1;
                this.item = {};
            },
            getItems(){
                if(this.user)
                    this.$store.dispatch('pedido/list/getItems', '/pedidos?user=' + this.user.id);
                else
                    this.$store.dispatch('pedido/list/getItems');
            },
        },
        created() {
            this.$store.dispatch('user/update/reset');
            if (this.users.length == 0) {
                this.$store.dispatch('user/list/getItems');
            }
            if(typeof this.$route.params.user != typeof undefined){
                this.$store.dispatch('user/update/retrieve', '/users/' + decodeURIComponent(this.$route.params.user)).then(() =>{
                    this.getItems();
                })
            }
            else{
                this.getItems();
            }
        }
    }
</script>
