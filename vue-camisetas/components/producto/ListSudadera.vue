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
                <span>Añadir Sudadera</span>
            </v-tooltip>
            <v-alert type="info" :value="true" v-show="items.length == 0" class="mt-2" style="width: 100%">
                No hay elementos para mostrar
            </v-alert>
            <div v-show="items.length != 0">
                <v-card-title>
                    <v-flex headline>
                        Sudaderas
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
                        :rows-per-page-items="[{'text':'All','value':-1}, 10,5,25]"
                        no-data-text=""
                        :disable-initial-sort="true"
                >
                    <template slot="items" slot-scope="props">
                        <td style="width: 100px; text-align: center;" class="py-1"><img v-if="props.item.imagen" class="img-style" @click="zoom(getImageUrl(props.item.imagen.path))"
                                                                                        :alt="props.item.imagen.name"
                                                                                        :src="getImageUrl(props.item.imagen.path)"/>
                        </td>
                        <td>{{ props.item.nombre }}</td>
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
    import zoom from 'vue-image-zoom';
    import 'vue-image-zoom/dist/vue-image-zoom.css';

    import Vue from 'vue'
    Vue.use(zoom);

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
                return this.editedIndex === -1 ? 'Crear Sudadera' : 'Editar Sudadera'
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
            zoom(img){
                this.$zoom(img, {
                    allowZoom:          true,  // When false, zooming is not available. Image will be shown on 100% size.
                    autoScale:          true,  // When true, if the image is larger than the screen, it will be automatically scaled down until suitable. Along with 'allowZoom'
                    closeOnClickModal:  false, // When false, clicking modal layer will close the viewer.
                });
            },
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
            cleanProduct() {
                this.img = null;
                this.producto = {nombre: ''};
                document.getElementById('photo').value = "";
            },
            clickUpload() {
                document.getElementById('photo').click()
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
            editItem(item) {
                this.open()
                this.producto = Object.assign({}, item)
                this.editedIndex = this.items.indexOf(item);
                if (this.producto.imagen && this.producto.imagen.path)
                    this.img = this.getImageUrl(this.producto.imagen.path);
            },
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
                            this.$store.dispatch('producto/list/getItems', true);
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

                this.producto.sudadera = true;

                let data = new FormData(), $this = this, snackbarText = 'Se ha creado', producto = {};
                if (this.editedIndex > -1) {
                    producto = {
                        id: this.producto.id,
                        nombre: this.producto.nombre
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

                    if ($this.flag) {
                        $this.flag = false;
                        return;
                    }
                    $this.snackbarText = snackbarText;
                    $this.snackbar = true;
                    $this.$store.dispatch('producto/list/getItems', true);
                }).catch(function (error) {
                    $this.updateLoading = false;
                    $this.error(error);
                });
                this.close()
            }
        },
        created() {
            this.$store.dispatch('producto/list/setItems', []);
            this.$store.dispatch('producto/list/getItems', true);
        }
    }
</script>
<style>
    .img-style{
        max-width: 130px; max-height: 130px; vertical-align: middle; cursor: pointer;
    }
    @media only screen and (max-width : 550px) {
        .img-style{
            max-width: 150px; max-height: 150px;
        }
    }
</style>
