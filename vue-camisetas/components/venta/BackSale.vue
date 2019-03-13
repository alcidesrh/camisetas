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
                   v-show="loading || retrieveLoadingVenta || retrieveLoadingTalla">
        <v-progress-circular indeterminate :size="70" :width="3" color="success"></v-progress-circular>
      </v-container>
    </v-card>

    <v-card v-if="retrieved">
      <v-card-title>
        <v-layout>
          <v-flex headline>
            Reponer en el stock de {{retrieved.user.fullName}}
            <v-btn color="primary" @click="reponer(false)" small>Reponer</v-btn>
            <v-btn color="primary" @click="reponer(true)" small>Imprimir resumen</v-btn>
          </v-flex>
        </v-layout>
        <v-btn icon flat @click.native="closeVenta" class="modal-btn-close">
          <v-icon>close</v-icon>
        </v-btn>
      </v-card-title>
      <div v-if="retrieved">
        <v-data-table
                :headers="headers"
                :items="retrieved.productos"
                hide-actions
        >
          <template slot="items" slot-scope="props">
            <td>
              <v-chip>
                <img height="35" v-bind:src="getImageUrl(props.item.producto.imagen.path)"
                     class="py-1"/>
                <label class="pl-2 d-inline">{{ props.item.producto.nombre }}</label>
              </v-chip>
            </td>
            <td v-for="talla in props.item.tallas_table">
              <v-edit-dialog
                      :return-value.sync="talla.vendidas"
                      lazy
                      persistent
              >
                <div>{{talla.vendidas}}</div>
                <v-text-field
                        slot="input"
                        label="Cantidad"
                        v-model="talla.vendidas"
                        single-line
                        autofocus
                ></v-text-field>
              </v-edit-dialog>
            </td>
          </template>
        </v-data-table>
      </div>
    </v-card>
  </div>
</template>
<script>
    import {mapGetters} from 'vuex';
    import {API_HOST} from '../../config/_entrypoint';
    import fetch from '../../utils/fetch';

    export default {
        data() {
            return {
                fromUser: false,
                loading: false,
                headers: [],
                snackbar: false,
                snackbarText: '',
                snackbarColor: 'success',
                flag: false,
            }
        },
        computed: {
            ...mapGetters({
                retrieved: 'venta/update/retrieved',
                productos: 'producto/list/items',
                tallas: 'talla/list/items',
                retrieveLoadingVenta: 'venta/update/retrieveLoading',
                retrieveLoadingTalla: 'talla/list/loading',
            })
        },
        watch: {
            dialog(val) {
                val || this.close()
            },
            snackbar(val) {
                val || (this.snackbarColor = 'success')
            }
        },
        methods: {
            reponer(print){
                let update = [];
                this.retrieved.productos.forEach(producto => {
                    producto.tallas.forEach(talla => {
                        if(talla.vendidas != 0)
                        update.push({talla: talla.id, reponer: talla.vendidas})
                    })
                });
                this.loading = true;
                let body = print?JSON.stringify({tallas: update, print: print, venta: this.retrieved.id}):JSON.stringify({tallas: update, print: print});
                if(update.length){
                    fetch('/replenish',{
                        method: 'POST',
                        body: body,
                        credentials: "same-origin",
                    })
                        .then(response => response.json())
                        .then(data => {
                            this.loading = false;
                            if(print)
                            window.open(API_HOST+'/'+data);
                        })
                        .catch(e => {
                            this.loading = false;
                            this.error(e.message)
                        });
                }
            },
            closeVenta() {
                if (this.fromUser)
                    this.$router.push({name: 'VentaList', params: {user: this.fromUser}})
                else
                    this.$router.push({name: 'VentaList'})
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
            getItem() {
                this.$store.dispatch('venta/update/retrieve', '/ventas/' + decodeURIComponent(this.$route.params.id)).then(() => {
                    this.setTable();
                });
            },
            setTable() {
                let $this = this, tallas = [];
                this.headers.push({text: '', value: ''});
                this.tallas.forEach(item => {
                    $this.headers.push({text: item.nombre, value: 'nombre'});
                    $this.retrieved.productos.forEach(item2 => {
                        if (typeof item2.tallas_table == typeof undefined)
                            item2.tallas_table = []
                        let filter = item2.tallas.filter(item3 => item3.talla.id == item.id);
                        if (filter.length){
                            item2.tallas_table.push(filter[0]);
                        }
                        else
                            item2.tallas_table.push(false);
                    });
                    tallas = [];
                });
            }
        },
        created() {
            this.$store.dispatch('venta/update/reset');
            if (typeof this.$route.params.user != typeof undefined)
                this.fromUser = decodeURIComponent(this.$route.params.user);

            if (!this.tallas.length)
                this.$store.dispatch('talla/list/getItems').then(() => {
                    this.getItem();
                });
            else
                this.getItem();
        }
    }
</script>
