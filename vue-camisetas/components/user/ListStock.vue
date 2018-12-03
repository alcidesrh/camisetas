<template>
    <v-container>
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
                         v-show="loading || deleteLoading">
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
                       @click="$router.push({name: 'StockCreate'})"
                >
                    <v-icon>add</v-icon>
                </v-btn>
                <span>Añadir Stock</span>
            </v-tooltip>
            <v-alert type="info" :value="true" v-show="items.length == 0" class="mt-2" style="width: 100%">
                No hay elementos para mostrar
            </v-alert>
            <div v-show="items.length != 0">
                <v-card-title>
                    <v-flex headline v-if="user">
                        Stocks de {{user.nombre + ' ' + user.apellidos}}
                    </v-flex>
                    <v-spacer></v-spacer>
                    <v-spacer></v-spacer>
                    <v-spacer></v-spacer>
                    <v-spacer></v-spacer>
                    <v-spacer></v-spacer>
                    <v-text-field
                            append-icon="search"
                            label="Buscar"
                            single-line
                            hide-details
                            v-model="search"
                    ></v-text-field>
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
                                <v-btn icon class="mx-0"  @click="$router.push({name: 'ListStock', params: {id: props.item['id']} })" slot="activator">
                                    <v-icon color="orange">visibility</v-icon>
                                </v-btn>
                                <span>Ver Stocks</span>
                            </v-tooltip>
                            <v-btn icon class="mx-0"
                                   @click="$router.push({name: 'StockUpdate', params: {id: props.item['id'], fromUser: user.id} })">
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
    </v-container>
</template>
<script>
    import {mapGetters} from 'vuex';
    import {API_HOST, API_PATH} from '../../config/_entrypoint';
    import moment from 'moment';

    export default {
        data() {
            return {
                stock: {user: false, productos: [], stock: []},
                items: [],
                loading: false,
                valid: true,
                search: '',
                headers: [
                    {text: 'Creado', value: 'createAt'},
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
                item: {name: ''},
                fieldRule: [
                    v => !!v || 'Este campo es requerido'
                ]
            }
        },
        computed: {
            ...mapGetters({
                deletedItem: 'stock/del/deleted',
                errorDelete: 'stock/del/error',
                deleteLoading: 'stock/del/loading',
                user: 'user/update/retrieved',
                productos: 'producto/list/items',
                tallas: 'talla/list/items'
            })
        },
        watch: {
            dialog(val) {
                val || this.close()
            },
            errorDelete(message) {
                this.error(message);
            },
            snackbar(val) {
                val || (this.snackbarColor = 'success')
            }
        },
        methods: {
            formatDate(date) {
                return moment(date.date).format('DD/MM/YYYY');
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
            open() {
                this.$refs.form.reset();
                this.editedIndex = -1;
                this.dialog = true;
            },
            deleteItem(item) {
                if (confirm('Seguro quieres eliminar este elemento?')) {

                    this.$store.dispatch('stock/del/delete', item).then(
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
            getItems(){
                let link = API_HOST + API_PATH + '/stocks-user/' + this.user.id;
                this.loading = true;
                fetch(link, {
                    method: 'POST',
                    credentials: "same-origin"
                })
                    .then(response => response.json())
                    .then(response => {
                        this.items = response['hydra:member'];
                        this.loading = false;
                    }).catch(e => {
                    this.error(e.message)
                });
            },
            close() {
                this.dialog = false;
                this.editedIndex = -1;
                this.item = {};
            },
        },
        created() {
            this.loading = true;
            let id = decodeURIComponent(this.$route.params.id);
            this.$store.dispatch('user/update/retrieve', '/users/' + id).then(() =>{
                this.getItems();
            })
        }
    }
</script>
