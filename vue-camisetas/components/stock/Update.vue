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
                         v-show="loading || updateLoading">
                <v-progress-circular indeterminate :size="70" :width="3" color="success"></v-progress-circular>
            </v-container>
        </v-card>
        <v-card>
            <v-card-title>
                <span class="headline">Editar Stock</span>
                <v-btn icon flat @click.native="closeUpdate" class="modal-btn-close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-form v-model="valid" ref="form" lazy-validation class="ml-3" v-if="retrieved">
                <v-card-text>
                    <v-layout row wrap v-if="!loading && retrieved" class="my-4">
                        <v-flex xs12>
                            <strong>Usuario:</strong> {{retrieved.user.fullName}}
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex md6>
                            <v-select
                                    v-model="productosSelected"
                                    :items="productosToSelected"
                                    :rules="fieldRule"
                                    label="Seleccionar productos"
                                    prepend-icon="add_shopping_cart"
                                    multiple
                                    required
                                    item-text="nombre"
                                    return-object
                            >
                                <template slot="selection" slot-scope="data">
                                    <v-chip style="padding-right: 10px">
                                        <img height="35" v-bind:src="getImageUrl(data.item.imagen.path)"
                                             class="py-1"/>
                                        <label class="pl-2 d-inline">{{ data.item.nombre }}</label>
                                    </v-chip>
                                </template>
                                <!--<template slot="item" slot-scope="data">-->
                                    <!--<template v-if="typeof data.item !== 'object'">-->
                                        <!--No hay datos disponibles-->
                                    <!--</template>-->
                                    <!--<template v-else>-->
                                        <!--<v-checkbox-->
                                                <!--v-model="productosCheckBox[productos.indexOf(data.item)]"-->
                                        <!--&gt;-->
                                            <!--<div slot="label"-->
                                                 <!--style="display: flex;align-items: center; justify-content: center">-->
                                                <!--<div class="d-inline ml-2">{{data.item.nombre}}</div>-->
                                                <!--<div class="d-inline ml-1">-->
                                                    <!--<img height="50" v-bind:src="getImageUrl(data.item.imagen.path)"-->
                                                         <!--class="py-2"/>-->
                                                <!--</div>-->
                                            <!--</div>-->
                                        <!--</v-checkbox>-->
                                    <!--</template>-->
                                <!--</template>-->
                            </v-select>
                        </v-flex>
                        <v-flex md6 pl-3>
                            <v-tooltip top><v-icon slot="activator" @click="asc = !asc" >sort_by_alpha</v-icon><span>Ordernar por nombre {{!asc?'ascendentemente':'descendentemente'}}</span></v-tooltip>
                            <v-radio-group v-model="sort" row style="display: inline-block">
                                <v-radio label="Todos" value="1"></v-radio>
                                <v-radio label="Camisetas" value="2"></v-radio>
                                <v-radio label="Sudadera" value="3"></v-radio>
                            </v-radio-group>

                        </v-flex>
                    </v-layout>
                    <v-layout row wrap class="mt-3" v-if="stock.length">
                        <v-flex xs12>
                            Asignar cantidad por talla para todos los productos:
                        </v-flex>
                        <div style="margin-left: 72px; display: flex">
                        <v-flex style="max-width: 50px" class="mx-3" v-for="(item, index) in tallas" :key="item.id">
                            <v-text-field
                                    v-model="stock[index].stock"
                                    v-on:keyup="stock[index].stock = validNumber(index, $event)?'':stock[index].stock"
                            >
                                <label slot="label" style="font-size: 14px">{{item.nombre}}</label>
                            </v-text-field>
                        </v-flex>
                        </div>
                    </v-layout>
                    <v-layout row wrap class="mt-3" v-if="productosSelected2.length">
                        <v-flex lg12>
                            Asignar cantidad por talla por producto: <strong>{{getTotal()}} total</strong>
                        </v-flex>
                        <v-flex lg12 class="mt-2">
                            <v-list two-line>
                                <template v-for="producto,index in productosSelected2">

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
                                                              v-model="productosSelected2[index].tallas[index2].stock"
                                                              v-on:keyup="productosSelected2[index].tallas[index2].stock = validNumber2(index, index2, $event)?0:productosSelected2[index].tallas[index2].stock"
                                                >
                                                    <label slot="label" style="font-size: 14px">{{talla.nombre}}</label>
                                                </v-text-field>
                                                <v-text-field style="max-width: 50px; display: inline-block"
                                                              class="mx-3"
                                                              v-model="productosSelected2[index].tallas.reduce((el, prev2) => {if(!Number.isInteger(parseInt(el.stock)))el.stock = 0;if(!Number.isInteger(parseInt(prev2.stock)))prev2.stock = 0;return {stock: parseInt(el.stock) + parseInt(prev2.stock)}}).stock"
                                                              disabled
                                                >
                                                    <label slot="label" style="font-size: 14px">Total</label>
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
                        <v-btn color="primary darken-1" flat @click.native="closeUpdate">
                            Cancelar
                        </v-btn>
                        <v-btn color="primary darken-1" flat @click="save">Guardar</v-btn>
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
                sort: '1',
                asc: false,
                item: {user: false, productos: [], stock: []},
                productosSelected: [],
                productosToSelected: [],
                productosSelected2: [],
                productosCheckBox: [],
                stock: [],
                fromUser: false,
                loading: false,
                valid: true,
                search: '',
                headers: [
                    {text: 'Nombre', value: 'nombre'},
                    {text: '', value: ''}
                ],
                snackbar: false,
                snackbarText: '',
                snackbarColor: 'success',
                flag: false,
                fieldRule: [
                    v => !!v || 'Este campo es requerido'
                ]
            }
        },
        computed: {
            ...mapGetters({
                errorUpdate: 'stock/update/updateError',
                retrieved: 'stock/update/retrieved',
                updateLoading: 'stock/update/updateLoading',
                productos: 'producto/list/items',
                tallas: 'talla/list/items'
            })
        },
        watch: {
            stock: {
                handler: function(newValue) {
                    let $this = this, cont = 0;
                    newValue.forEach(function(item){
                        if(Number.isInteger(parseInt(item.stock))){
                            $this.productosSelected2.forEach(function(item2){
                                item2.tallas[cont].stock = item.stock;
                            })
                        }
                        else{
                            $this.productosSelected2.forEach(function(item2){
                                item2.tallas[cont].stock = 0;
                            })
                        }
                        cont++;
                    });
                },
                deep: true
            },
            sort(val) {
                if (val == 1)
                    this.productosToSelected = this.productos
                else if (val == 2)
                    this.productosToSelected = this.productos.filter(item => !item.sudadera)
                else
                    this.productosToSelected = this.productos.filter(item => item.sudadera);
            },
            asc(val){
                let sort = (a, b) => {
                    if(val){
                        if(a.nombre.toLowerCase() < b.nombre.toLowerCase()) { return -1; }
                        if(a.nombre.toLowerCase() > b.nombre.toLowerCase()) { return 1; }
                    }
                    else{
                        if(a.nombre.toLowerCase() > b.nombre.toLowerCase()) { return -1; }
                        if(a.nombre.toLowerCase() < b.nombre.toLowerCase()) { return 1; }
                    }

                    return 0;
                }
                let productos = this.productos.sort(sort);
                this.$store.dispatch('producto/list/setItems', productos);
                this.productosSelected = this.productosSelected.sort(sort);
                this.productosSelected2 = this.productosSelected2.sort(sort);
            },
            errorUpdate(message) {
                this.error(message);
            },
            snackbar(val) {
                val || (this.snackbarColor = 'success')
            },
            productosSelected(){
                this.productosSelected2 = [];
                let $this = this;
                this.productosSelected.forEach(item => {
                    $this.productosSelected2.push(Object.assign({}, item));
                })
            }
        },
        methods: {
            getTotal(){
                let total = 0;
                this.productosSelected2.forEach(item => {
                    total = total + item.tallas.reduce((el, prev2) => {if(!Number.isInteger(parseInt(el.stock))) el.stock = 0; if(!Number.isInteger(parseInt(prev2.stock))) prev2.stock = 0;return {stock: parseInt(el.stock) + parseInt(prev2.stock)}}).stock;
                })
                return total;
            },
            closeUpdate() {
                if (this.fromUser)
                    this.$router.push({name: 'StockList', params: {user: this.fromUser}})
                else
                    this.$router.push({name: 'StockList'})
            },
            validNumber(index, event) {
                if ((event.keyCode < 48 || (event.keyCode > 57 && event.keyCode < 96 || event.keyCode > 105)) && (event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 13)) {
                    return true;
                }
                return false;
            },
            validNumber2(index, index2, event) {
                if ((event.keyCode < 48 || (event.keyCode > 57 && event.keyCode < 96 || event.keyCode > 105)) && (event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 13)) {
                   return true;
                }
                return false;
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
            setStock() {
                this.item.productos = [];
                let $this = this;
                this.productosSelected2.forEach(item => {
                    let producto = {id: item.id, stock: item.tallas};
                    if (typeof item.producto_stock != typeof undefined)
                        producto.producto_stock = item.producto_stock;
                    $this.item.productos.push(producto);
                });
                this.item.stock = this.stock


            },
            save() {
                if (!this.$refs.form.validate()) return;
                if (!this.productosSelected2.length) {
                    this.error('No ha elegido ningún producto');
                    return;
                }
                this.setStock();
                this.$store.dispatch('stock/update/update', {
                    id: this.item.id,
                    values: this.item
                }).then(
                    () => {
                        if (this.flag) {
                            this.flag = false;
                            return;
                        }
                        this.snackbarText = 'Se ha editado';
                        this.snackbar = true;
                        this.closeUpdate();

                    });
            },
            getItem() {
                this.$store.dispatch('stock/update/retrieve', '/stocks/' + decodeURIComponent(this.$route.params.id)).then(() => {
                    this.item.id = this.retrieved.id;
                    let $this = this;

                    this.tallas.forEach((item) => {
                        $this.stock.push({id: item.id, stock: null});
                    })

                    this.productos.forEach(item => {
                        item.tallas = [];
                        $this.tallas.forEach((item2) => {
                            item.tallas.push({id: item2.id, stock: null});
                        })
                        $this.productosCheckBox.push(false);
                    });

                    this.retrieved.productos.forEach(item => {
                        let result = $this.productos.filter(item2 => item2.id == item.producto.id);
                        $this.productosCheckBox[$this.productos.indexOf(result[0])] = true
                        result[0].tallas.forEach((talla, index) => {
                            if (typeof item.tallas[index] != typeof undefined)
                                talla.stock = item.tallas[index].cantidad;
                            else
                                talla.stock = "";
                        });
                        result[0].producto_stock = item.id;
                        this.productosSelected.push(Object.assign({}, result[0]));
                    });
                    this.loading = false;
                });
            }
        },
        created() {
            this.loading = true;

            if (typeof this.$route.params.user != typeof undefined)
                this.fromUser = decodeURIComponent(this.$route.params.user);
            this.$store.dispatch('talla/list/getItems').then(() => {
                this.$store.dispatch('producto/list/getItems', 'all').then(() => {
                    this.productosToSelected = this.productos;
                    this.getItem();
                })
            });
        }
    }
</script>
