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
                         v-show="loading || deleteLoading || updateLoading">
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
                       @click="open"
                >
                    <v-icon>add</v-icon>
                </v-btn>
                <span>Añadir Producto</span>
            </v-tooltip>
            <v-alert type="info" :value="true" v-show="items.length == 0" class="mt-2" style="width: 100%">
                No hay elementos para mostrar
            </v-alert>
            <div v-show="items.length != 0">
                <v-card-title>
                    <v-flex headline>
                        Camisetas
                    </v-flex>
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
                        <td style="width: 100px; text-align: center;" class="py-1"><img v-if="props.item.imagen"
                                                                                        :alt="props.item.imagen.name"
                                                                                        :src="getImageUrl(props.item.imagen.path)"
                                                                                        style="max-width: 100px; max-height: 100px; vertical-align: middle"/>
                        </td>
                        <td>{{ props.item.nombre }}</td>
                        <!--<td v-for="item in props.item.talla_stock">-->
                            <!--<v-edit-dialog-->
                                    <!--:return-value.sync="item.cantidad"-->
                                    <!--large-->
                                    <!--lazy-->
                                    <!--persistent-->
                            <!--&gt;-->
                                <!--<div>{{ item.cantidad }}</div>-->
                                <!--<div slot="input" class="mt-3 title">En Stock</div>-->
                                <!--<v-text-field-->
                                        <!--slot="input"-->
                                        <!--v-model="item.cantidad"-->
                                        <!--label="cantidad"-->
                                        <!--single-line-->
                                        <!--autofocus-->
                                <!--&gt;</v-text-field>-->
                            <!--</v-edit-dialog>-->
                        <!--</td>-->
                        <td class="text-xs-center">
                            <v-btn icon class="mx-0" @click="editItem(props.item)">
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
        <v-dialog v-model="dialog" max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                    <v-btn icon flat @click.native="dialog = false" class="modal-btn-close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-form v-model="valid" ref="form" lazy-validation v-on:submit.prevent="save" class="pl-4">
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <input type="file" style="display: none" id="photo" @change="photoProccess" ref="photo">
                                <img v-show="img" :src="img" style="max-width: 100px; vertical-align: middle"/>
                                <v-btn small color="primary" @click="clickUpload" class="d-inline">Subir Imagen</v-btn>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs8 lg6>
                                <v-text-field
                                        label="Nombre"
                                        v-model="producto.nombre"
                                        :rules="fieldRule"
                                        required
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                        <!--<v-layout row wrap class="mt-3">-->
                            <!--<v-flex xs2>-->
                                <!--Tallas:-->
                            <!--</v-flex>-->
                            <!--<v-flex xs3 lg4>-->
                                <!--En Stock:-->
                            <!--</v-flex>-->
                        <!--</v-layout>-->
                        <!--<v-layout row wrap align-center v-for="(item, index) in tallas" :key="item.id">-->
                            <!--<v-flex xs2>-->
                                <!--<v-checkbox-->
                                        <!--:label="item.nombre"-->
                                        <!--v-model="producto.tallas[index]"-->
                                        <!--@change="cleanStock(index)"-->
                                        <!--:value="item"-->
                                        <!--hide-details-->
                                <!--&gt;</v-checkbox>-->
                            <!--</v-flex>-->
                            <!--<v-flex xs1>-->
                                <!--<v-text-field-->
                                        <!--v-model="producto.stock[index]"-->
                                        <!--:disabled="!producto.tallas[index]"-->
                                        <!--v-on:keyup="validNumber(index, $event)"-->
                                <!--&gt;</v-text-field>-->
                            <!--</v-flex>-->
                        <!--</v-layout>-->
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary darken-1" flat @click.native="close">Cancelar</v-btn>
                        <v-btn color="primary darken-1" flat type="submit">Guardar</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </v-container>
</template>
<script>
    import {mapGetters} from 'vuex';
    import axios from 'axios';
    import {API_HOST, API_PATH} from '../../config/_entrypoint';

    export default {
        data() {
            return {
                valid: true,
                search: '',
                headers: [
                    {text: '', value: ''},
                    {text: 'Nombre', value: 'nombre'},
                    {text: '', value: ''}
                ],
                dialog: false,
                editedIndex: -1,
                snackbar: false,
                snackbarText: '',
                snackbarColor: 'success',
                flag: false,
                producto: {nombre: '', imgName: null, imgSrc: null},
                img: null,
                fieldRule: [
                    v => !!v || 'Este campo es requerido'
                ],
                updateLoading: false
            }
        },
        computed: {
            formTitle() {
                return this.editedIndex === -1 ? 'Crear Producto' : 'Editar Producto'
            },
            ...mapGetters({
                deletedItem: 'producto/del/deleted',
                errorList: 'producto/list/error',
                errorCreate: 'producto/create/error',
                errorUpdate: 'producto/update/updateError',
                errorDelete: 'producto/del/error',
                items: 'producto/list/items',
                loading: 'producto/list/loading',
                view: 'producto/list/view',
                created: 'producto/create/created',
                deleteLoading: 'producto/del/loading',
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
            // cleanStock(index) {
            //     if (typeof this.producto.tallas[index] != typeof undefined && !this.producto.tallas[index])
            //         this.producto.stock[index] = null;
            // },
            photoProccess() {
                let $this = this;
                let reader = new FileReader();
                let file = this.$refs.photo.files[0];

                reader.onloadend = function () {
                    $this.img = reader.result;
                    $this.producto.imgSrc = reader.result;
                }

                if (this.$refs.photo.files.length) {
                    reader.readAsDataURL(file); //reads the data as a URL
                    this.producto.imgName = file.name;
                } else {
                    this.img = null;
                    this.producto.imgName = null;
                    this.producto.imgSrc = null
                }
            },
            getImageUrl(path) {
                return API_HOST + '/' + path
            },
            // validNumber(index, event) {
            //     if ((event.keyCode < 48 || (event.keyCode > 57 && event.keyCode < 96 || event.keyCode > 105)) && (event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 13)) {
            //         let array = this.producto.stock;
            //         this.producto.stock = [];
            //         array.forEach(function (item, index2) {
            //             if (index2 == index)
            //                 array[index] = null;
            //         });
            //         this.producto.stock = array;
            //     }
            // },
            cleanProduct() {
                this.img = null;
                this.producto = {nombre: ''};
                // this.producto = {nombre: '', tallas: [], stock: [], imgName: null, imgSrc: null};
                document.getElementById('photo').value = "";
            },
            clickUpload() {
                document.getElementById('photo').click()
            },
            // updateTable() {
            //     let $this = this;
            //     this.tallas.forEach(item => {
            //         $this.productos.forEach(item2 => {
            //             if (typeof item2.talla_stock == typeof undefined)
            //                 item2.talla_stock = [];
            //             let talla = item2.tallas.filter(item3 => item3.talla.id == item.id);
            //             if (talla.length)
            //                 item2.talla_stock.push(talla[0]);
            //             else
            //                 item2.talla_stock.push({talla: item, cantidad: 0});
            //         });
            //     });
            //     this.items = this.productos;
            // },
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
            editItem(item) {
                this.open()
                this.producto = Object.assign({}, item)
                this.editedIndex = this.items.indexOf(item);
                if (this.producto.imagen && this.producto.imagen.path)
                    this.img = this.getImageUrl(this.producto.imagen.path);
                //     this.img = this.getImageUrl(this.producto.imagen.path);
                // this.producto = Object.assign({}, item);
                // if (this.producto.imagen && this.producto.imagen.path)
                //     this.img = this.getImageUrl(this.producto.imagen.path);
                // this.producto.stock = [];
                // let $this = this;
                // this.producto.talla_stock.forEach((item2, index) => {
                //     if (typeof item2.cantidad != typeof undefined && item2.cantidad != 0)
                //         $this.producto.stock[index] = item2.cantidad;
                //     else
                //         $this.producto.stock[index] = null;
                //
                // });
                // this.tallasOrden(this.producto);
                // this.editedIndex = this.items.indexOf(item);
                // this.dialog = true;
            },
            // tallasOrden(item) {
            //     let tallas = [];
            //     this.tallas.forEach((talla, index) => {
            //         let array = item.tallas.filter(talla2 => talla.id == talla2.talla.id)
            //         if (array.length) {
            //             tallas[index] = talla;
            //         }
            //         else
            //             tallas[index] = null;
            //     });
            //     item.tallas = tallas;
            // },
            deleteItem(item) {
                const index = this.items.indexOf(item)
                if (confirm('Seguro quieres eliminar este elemento?')) {

                    this.$store.dispatch('producto/del/delete', item).then(
                        () => {
                            if (this.flag) {
                                this.flag = false;
                                return;
                            }
                            this.items.splice(index, 1)
                            this.snackbarText = 'Se ha eliminado';
                            this.snackbar = true;
                            this.$store.dispatch('producto/list/getItems').then(() => {
                                // $this.updateTable();
                            });
                        })
                }
            },
            close() {
                this.dialog = false;
                this.editedIndex = -1;
                this.cleanProduct();
            },
            save() {
                if (!this.$refs.form.validate()) return;
                // let tallas = [];
                // this.producto.tallas.forEach(item => {
                //     if (item && typeof item.talla != typeof undefined)
                //         tallas.push({talla: item.talla});
                //     else
                //         tallas.push(item ? item.id : null);
                // })
                // this.producto.tallas = tallas;
                let data = new FormData(), $this = this, snackbarText = 'Se ha creado', producto = {};
                if (this.editedIndex > -1) {
                    producto = {
                        id: this.producto.id,
                        nombre: this.producto.nombre,
                        // tallas: this.producto.tallas,
                        // stock: this.producto.stock
                    };
                    if (typeof this.producto.imgName != typeof undefined) {
                        producto.imgName = this.producto.imgName;
                        producto.imgSrc = this.producto.imgSrc;
                    }
                    snackbarText = 'Se ha editado';
                }
                else {
                    Object.assign(producto, this.producto);
                }
                data.append('producto', JSON.stringify(producto));
                this.updateLoading = true;
                axios.post(API_HOST + API_PATH + '/save-producto', data).then(function (response) {
                    $this.updateLoading = false;
                    // $this.cleanProduct();
                    if ($this.flag) {
                        $this.flag = false;
                        return;
                    }
                    $this.snackbarText = snackbarText;
                    $this.snackbar = true;
                    $this.$store.dispatch('producto/list/getItems').then(() => {
                        // $this.updateTable();
                    });
                }).catch(function (error) {
                    $this.updateLoading = false;
                    $this.error(error);
                });
                this.close()
            }
        },
        created() {
            this.$store.dispatch('producto/list/getItems').then(() => {console.log(this)
                // this.$store.dispatch('talla/list/getItems').then(() => {
                //     let $this = this;
                //     this.tallas.forEach(item => {
                //         $this.headers.push({text: item.nombre, value: item.nombre});
                //         $this.productos.forEach(item2 => {
                //             if (typeof item2.talla_stock == typeof undefined)
                //                 item2.talla_stock = [];
                //
                //             let talla = item2.tallas.filter(item3 => item3.talla.id == item.id);
                //             if (talla.length)
                //                 item2.talla_stock.push(talla[0]);
                //             else
                //                 item2.talla_stock.push({talla: item, cantidad: 0});
                //         });
                //     });
                //     this.headers.push({text: '', value: ''});
                //     this.items = this.productos;
                // });
            });
        }
    }
</script>
