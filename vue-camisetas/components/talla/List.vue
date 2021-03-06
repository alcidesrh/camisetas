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
               @click="open"
        >
          <v-icon>add</v-icon>
        </v-btn>
        <span>Añadir Talla</span>
      </v-tooltip>
      <v-alert type="info" :value="true" v-show="items.length == 0" class="mt-2" style="width: 100%">
        No hay elementos para mostrar
      </v-alert>
      <div v-show="items.length != 0">
        <v-card-title>
          <v-flex headline>
            Tallas
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
            <td>{{ props.item.nombre }}</td>
            <td class="justify-center layout px-0">
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
    <v-dialog v-model="dialog" max-width="400px">

      <v-card>
        <v-card-title>
          <span class="headline">{{ formTitle }}</span>
          <v-btn icon flat @click.native="dialog = false" class="modal-btn-close">
            <v-icon>close</v-icon>
          </v-btn>
        </v-card-title>
        <v-form v-model="valid" ref="form" lazy-validation v-on:submit.prevent="save">
          <v-card-text>
            <v-text-field
                    label="Nombre"
                    v-model="item.nombre"
                    :rules="fieldRule"
                    required
            ></v-text-field>
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

    export default {
        data() {
            return {
                valid: true,
                search: '',
                headers: [
                    {text: 'Nombre', value: 'nombre'},
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
                return this.editedIndex === -1 ? 'Crear Talla' : 'Editar Talla'
            },
            ...mapGetters({
                deletedItem: 'talla/del/deleted',
                errorList: 'talla/list/error',
                errorCreate: 'talla/create/error',
                errorUpdate: 'talla/update/updateError',
                errorDelete: 'talla/del/error',
                items: 'talla/list/items',
                loading: 'talla/list/loading',
                view: 'talla/list/view',
                created: 'talla/create/created',
                deleteLoading: 'talla/del/loading',
                updateLoading: 'talla/update/updateLoading',
                createLoading: 'talla/create/loading'
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
                this.item = Object.assign({}, item)
                this.editedIndex = this.items.indexOf(item);
            },
            deleteItem(item) {
                const index = this.items.indexOf(item)
                if (confirm('Seguro quieres eliminar este elemento?')) {

                    this.$store.dispatch('talla/del/delete', item).then(
                        () => {
                            if (this.flag) {
                                this.flag = false;
                                return;
                            }
                            this.items.splice(index, 1)
                            this.snackbarText = 'Se ha eliminado';
                            this.snackbar = true;
                            this.$store.dispatch('talla/list/getItems');
                        })
                }
            },
            close() {
                this.dialog = false;
                this.editedIndex = -1;
                this.item = {};
            },
            save() {
                if (!this.$refs.form.validate()) return;
                let itemAux = {};
                Object.assign(itemAux, this.item);
                if (this.editedIndex > -1) {
                    let editedIndex = this.editedIndex;
                    this.$store.dispatch('talla/update/update', {
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
                            this.$store.dispatch('talla/list/getItems');
                        });
                } else {
                    this.$store.dispatch('talla/create/create', itemAux).then(
                        () => {
                            if (this.flag) {
                                this.flag = false;
                                return;
                            }
                            this.items.unshift(this.created);
                            this.snackbarText = 'Se ha creado';
                            this.snackbar = true;
                            this.$store.dispatch('talla/list/getItems');
                        });
                }
                this.close()
            }
        },
        created() {
            if (this.items.length == 0) {
                this.$store.dispatch('talla/list/getItems');
            }
        }
    }
</script>
