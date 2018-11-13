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
                         v-show="loading || createLoading">
                <v-progress-circular indeterminate :size="70" :width="3" color="success"></v-progress-circular>
            </v-container>
        </v-card>
        <v-card>
            <v-card-title>
                <span class="headline">Crear Pedido</span>
                <v-btn icon flat @click.native="$router.push({name: 'PedidoList'})" class="modal-btn-close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-form v-model="valid" ref="form" lazy-validation v-on:submit.prevent="save" class="ml-3">
                <v-card-text>
                    <v-layout row wrap>
                        <v-flex md4>
                            <v-select
                                    v-model="pedido.user"
                                    :items="users"
                                    :rules="fieldRule"
                                    item-value="id"
                                    label="Sleccionar usuario"
                                    prepend-icon="person"
                                    required
                            >
                                <template slot="selection" slot-scope="data">
                                    <label>{{ data.item.nombre}} {{ data.item.apellidos }}</label>
                                </template>
                                <template slot="item" slot-scope="data">
                                    <template v-if="typeof data.item !== 'object'">
                                        No hay datos disponibles
                                    </template>
                                    <template v-else>
                                        <label>{{ data.item.nombre}} {{ data.item.apellidos }}</label>
                                    </template>
                                </template>
                            </v-select>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex md6>
                            <v-select
                                    v-model="productosSelected"
                                    :items="productos"
                                    :rules="fieldRule"
                                    label="Seleccionar productos"
                                    prepend-icon="add_shopping_cart"
                                    multiple
                                    required
                            >
                                <template slot="selection" slot-scope="data">
                                    <v-chip>
                                        <img height="35" v-bind:src="getImageUrl(data.item.imagen.path)"
                                             class="py-1"/>
                                        <label class="pl-2 d-inline">{{ data.item.nombre }}</label>
                                    </v-chip>
                                </template>
                                <template slot="item" slot-scope="data">
                                    <template v-if="typeof data.item !== 'object'">
                                        No hay datos disponibles
                                    </template>
                                    <template v-else>
                                        <v-checkbox>
                                            <div slot="label"
                                                 style="display: flex;align-items: center; justify-content: center">
                                                <div class="d-inline ml-2">{{data.item.nombre}}</div>
                                                <div class="d-inline ml-1">
                                                    <img height="50" v-bind:src="getImageUrl(data.item.imagen.path)"
                                                         class="py-2"/>
                                                </div>
                                            </div>
                                        </v-checkbox>
                                    </template>
                                </template>
                            </v-select>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap class="mt-3" v-if="stock.length">
                        <v-flex xs12>
                            Asignar cantidad por talla para todos los productos:
                        </v-flex>
                        <v-flex style="max-width: 50px" class="mx-3" v-for="(item, index) in tallas" :key="item.id">
                            <v-text-field
                                    v-model="stock[index].stock"
                                    v-on:keyup="validNumber(index, $event)"
                            >
                                <label slot="label" style="font-size: 14px">{{item.nombre}}</label>
                            </v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap class="mt-3" v-if="productosSelected.length">
                        <v-flex lg12>
                            Asignar cantidad por talla por producto:
                        </v-flex>
                        <v-flex lg12 class="mt-2">
                            <v-list two-line>
                                <template v-for="producto,index in productosSelected">

                                    <v-divider class="my-2"
                                               v-if="index > 0"
                                               :inset="true"
                                               :key="index"
                                    ></v-divider>

                                    <v-list-tile avatar>
                                        <v-list-tile-avatar>
                                            <img :src="getImageUrl(producto.imagen.path)">
                                        </v-list-tile-avatar>

                                        <v-list-tile-content class="pb-2">
                                            <v-list-tile-title>
                                                {{producto.nombre}}
                                            </v-list-tile-title>
                                            <v-list-tile-sub-title style="overflow: initial">
                                                <v-text-field style="max-width: 50px; display: inline-block"
                                                              class="mx-3" v-for="(talla, index2) in tallas"
                                                              :key="talla.id"
                                                              v-model="productosSelected[index].tallas[index2].stock"
                                                              v-on:keyup="validNumber2(index, index2, $event)"
                                                >
                                                    <label slot="label" style="font-size: 14px">{{talla.nombre}}</label>
                                                </v-text-field>
                                            </v-list-tile-sub-title>

                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>
                            </v-list>
                        </v-flex>
                    </v-layout>
                    <div class="text-xs-center mt-5 mb2">
                        <v-spacer></v-spacer>
                        <v-btn color="primary darken-1" flat @click.native="$router.push({name: 'PedidoList'})">
                            Cancelar
                        </v-btn>
                        <v-btn color="primary darken-1" flat type="submit">Guardar</v-btn>
                    </div>
                </v-card-text>

            </v-form>
        </v-card>
    </v-container>
</template>
<script>
    import {mapGetters} from 'vuex';
    import {API_HOST, API_PATH} from '../../config/_entrypoint';

    export default {
        data() {
            return {
                pedido: {user: false, productos: [], stock: []},
                productosSelected: [],
                stock: [],
                loading: false,
                valid: true,
                search: '',
                headers: [
                    {text: 'Nombre', value: 'nombre'},
                    {text: '', value: ''}
                ],
                dialog: false,
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
                deletedItem: 'pedido/del/deleted',
                errorList: 'pedido/list/error',
                errorDelete: 'pedido/del/error',
                deleteLoading: 'pedido/del/loading',
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
            errorDelete(message) {
                this.error(message);
            },
            snackbar(val) {
                val || (this.snackbarColor = 'success')
            }
        },
        methods: {
            validNumber(index, event) {
                if ((event.keyCode < 48 || (event.keyCode > 57 && event.keyCode < 96 || event.keyCode > 105)) && (event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 13)) {
                    let array = this.stock;
                    this.stock = [];
                    array.forEach(function (item, index2) {
                        if (index2 == index)
                            array[index] = {id: item.id, stock: null};
                    });
                    this.stock = array;
                }
            },
            validNumber2(index, index2, event) {
                if ((event.keyCode < 48 || (event.keyCode > 57 && event.keyCode < 96 || event.keyCode > 105)) && (event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 13)) {
                    let array = this.productosSelected[index].tallas;
                    this.productosSelected[index].tallas = [];
                    array.forEach(function (item, index3) {
                        if (index2 == index3)
                            array[index3] = {id: item.id, stock: null};
                    });
                    this.productosSelected[index].tallas = array;
                }
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
                this.dialog = true;
            },
            close() {
                this.dialog = false;
                this.item = {};
            },
            setPedido() {
                this.pedido.productos = [];
                let $this = this;
                this.productosSelected.forEach(item => {
                    $this.pedido.productos.push({id: item.id, stock: item.tallas});
                });
                this.pedido.stock = this.stock


            },
            save() {
                if (!this.$refs.form.validate()) return;
                if (!this.productosSelected.length) {
                    this.error('No ha elegido ningún producto');
                    return;
                }
                this.setPedido();
                this.$store.dispatch('pedido/create/create', this.pedido).then(
                    () => {
                        if (this.flag) {
                            this.flag = false;
                            return;
                        }
                        this.snackbarText = 'Se ha creado';
                        this.snackbar = true;
                        this.loading = true;
                        this.$store.dispatch('pedido/list/getItems').then(() => {
                            this.loading = false;
                            this.$router.push({name: 'PedidoList'})
                        })

                    });
                this.close()
            }
        },
        created() {
            this.loading = true;
            this.$store.dispatch('user/list/getItems');
            this.$store.dispatch('talla/list/getItems').then(() => {
                let $this = this;
                this.tallas.forEach((item) => {
                    $this.stock.push({id: item.id, stock: null});
                })
                this.$store.dispatch('producto/list/getItems').then(() => {

                    this.productos.forEach(item => {
                        item.tallas = [];
                        $this.tallas.forEach((item2) => {
                            item.tallas.push({id: item2.id, stock: null});
                        })
                    });
                    this.loading = false;
                });
            });
        }
    }
</script>
