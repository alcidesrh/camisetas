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
                         v-show="loading || deleteLoading || updateLoading || createLoading">
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
                    <v-flex headline>
                        Pedidos
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
                            <v-btn icon class="mx-0" @click="$router.push({name: 'PedidoUpdate', params: {id: props.item['id']} })">
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
                pedido: {user: false, productos: [], stock: []},
                valid: true,
                search: '',
                headers: [
                    {text: 'Creado', value: 'createAt'},
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
                item: {name: ''},
                fieldRule: [
                    v => !!v || 'Este campo es requerido'
                ]
            }
        },
        computed: {
            formTitle() {
                return this.editedIndex === -1 ? 'Crear Pedido' : 'Editar Pedido'
            },
            ...mapGetters({
                deletedItem: 'pedido/del/deleted',
                errorList: 'pedido/list/error',
                errorCreate: 'pedido/create/error',
                errorUpdate: 'pedido/update/updateError',
                errorDelete: 'pedido/del/error',
                items: 'pedido/list/items',
                loading: 'pedido/list/loading',
                view: 'pedido/list/view',
                created: 'pedido/create/created',
                deleteLoading: 'pedido/del/loading',
                updateLoading: 'pedido/update/updateLoading',
                createLoading: 'pedido/create/loading',
                users: 'user/list/items',
                productos: 'producto/list/items',
                tallas: 'talla/list/items'
            })
        },
        watch: {
            dialog(val) {
                val || this.close()
            },
            errorList(message) {
                this.error(message);
            },
            errorCreate(message) {
                this.error(message);
            },
            errorUpdate(message) {
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
            formatDate(date){
                return moment().format('DD/MM/YYYY');
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
                const index = this.items.indexOf(item)
                if (confirm('Seguro quieres eliminar este elemento?')) {

                    this.$store.dispatch('pedido/del/delete', item).then(
                        () => {
                            if (this.flag) {
                                this.flag = false;
                                return;
                            }
                            this.snackbarText = 'Se ha eliminado';
                            this.snackbar = true;
                            this.$store.dispatch('pedido/list/getItems');
                        })
                }
            },
            close() {
                this.dialog = false;
                this.editedIndex = -1;
                this.item = {};
            },
            save() {;
                if (!this.$refs.form.validate()) return;
                if(!this.pedido.productos.length){
                    this.error('No ha elegido ningún producto');
                    return;
                }
                let itemAux = {};
                Object.assign(itemAux, this.pedido);
                if (this.editedIndex > -1) {
                    let editedIndex = this.editedIndex;
                    this.$store.dispatch('pedido/update/update', {
                        item: this.items[editedIndex],
                        values: itemAux
                    }).then(
                        () => {
                            if (this.flag) {
                                this.flag = false;
                                return;
                            }
                            Object.assign(this.items[editedIndex], itemAux);
                            this.snackbarText = 'Se ha editado';
                            this.snackbar = true;
                            this.$store.dispatch('pedido/list/getItems');
                        });
                } else {
                    this.$store.dispatch('pedido/create/create', itemAux).then(
                        () => {
                            if (this.flag) {
                                this.flag = false;
                                return;
                            }
                            this.items.unshift(this.created);
                            this.snackbarText = 'Se ha creado';
                            this.snackbar = true;
                            this.$store.dispatch('pedido/list/getItems');
                        });
                }
                this.close()
            }
        },
        created() {
            if (this.items.length == 0) {
                this.$store.dispatch('pedido/list/getItems').then(() => {

                });
            }
        }
    }
</script>
